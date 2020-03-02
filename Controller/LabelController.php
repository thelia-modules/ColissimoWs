<?php
/*************************************************************************************/
/*      Copyright (c) Franck Allimant, CQFDev                                        */
/*      email : thelia@cqfdev.fr                                                     */
/*      web : http://www.cqfdev.fr                                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE      */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

/**
 * Created by Franck Allimant, CQFDev <franck@cqfdev.fr>
 * Date: 04/09/2019 21:51
 */
namespace ColissimoWs\Controller;

use ColissimoLabel\Model\ColissimoLabelQuery;
use ColissimoWs\ColissimoWs;
use ColissimoWs\Event\LabelEvent;
use ColissimoWs\Model\ColissimowsLabel;
use ColissimoWs\Model\ColissimowsLabelQuery;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\PdfEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Exception\TheliaProcessException;
use Thelia\Log\Tlog;
use Thelia\Model\ConfigQuery;
use Thelia\Model\ModuleQuery;
use Thelia\Model\OrderQuery;
use Thelia\Model\OrderStatusQuery;
use Thelia\Tools\URL;

class LabelController extends BaseAdminController
{
    /** @TODO : Compatibility with colissimo_label module */
    const LABEL_DIRECTORY = THELIA_LOCAL_DIR . 'colissimo-label';

    /**
     * @return mixed|\Symfony\Component\HttpFoundation\Response|StreamedResponse
     */
    public function export()
    {
        static $codesPaysEurope = [
            'DE',
            'AT',
            'BE',
            'BG',
            'CY',
            'HR',
            'DK',
            'ES',
            'EE',
            'FI',
            'FR',
            'GR',
            'HU',
            'IE',
            'IT',
            'LV',
            'LT',
            'MT',
            'LU',
            'NL',
            'PL',
            'PT',
            'CZ',
            'RO',
            'GB',
            'SK',
            'SI',
            'SE '
        ];

        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), array('ColissimoWs'), AccessManager::UPDATE)) {
            return $response;
        }

        $exportForm  = $this->createForm('colissimows_export_form');

        $files = $params = [];

        if (!@mkdir(self::LABEL_DIRECTORY) && !is_dir(self::LABEL_DIRECTORY)) {
            throw new TheliaProcessException("Failed to create directory " . self::LABEL_DIRECTORY);
        }

        try {
            $form = $this->validateForm($exportForm);

            $data = $form->getData();

            // Check status_id
            $newStatus = OrderStatusQuery::create()->findOneByCode($data['new_status']);

            ColissimoWs::setConfigValue("new_status", $data['new_status']);

            $weight_array = $data['weight'];
            $signed_array = $data['signed'];

            foreach($data['order_id'] as $orderId) {
                if (null !== $order = OrderQuery::create()->findPk($orderId)) {
                    if (! isset($weight_array[$orderId]) || 0 === (float)$weight_array[$orderId]) {
                        $weight = $order->getWeight();
                    } else {
                        $weight = (float) $weight_array[$orderId];
                    }

                    if ($weight === null) {
                        throw new \Exception($this->getTranslator()->trans("Please enter a weight for every selected order"));
                    }

                    if (array_key_exists ($orderId , $signed_array)){
                        $signed = $signed_array[$orderId];
                    } else {
                        $signed = false;
                    }

                    $event = (new LabelEvent($orderId))
                        ->setWeight($weight)
                        ->setSigned($signed);

                    $this->getDispatcher()->dispatch(ColissimoWs::GENERATE_LABEL_EVENT, $event);

                    if ($event->hasLabel() && $event->getColissimoWsLabel()->getError() === false) {
                        $fileType = ColissimoWs::getLabelFileType();

                        $labelFileName = self::LABEL_DIRECTORY . DS . $order->getRef() . '.' . $fileType;

                        file_put_contents($labelFileName, $event->getColissimoWsLabel()->getLabelData());

                        $files[] = $labelFileName;

                        $destinationEurope =
                            in_array(
                                strtoupper($order->getOrderAddressRelatedByDeliveryOrderAddressId()->getCountry()->getIsoalpha2()),
                                $codesPaysEurope
                            )
                        ;

                        /** Comment this to disable "no customs invoice template" error */
                        // Generate customs invoice for non-FR foreign shipping
                        if (!$destinationEurope) {
                            $files[] = $this->createCustomsInvoice($orderId, $order->getRef());

                            // We have a customs invoice !
                            $event
                                ->getColissimoWsLabel()
                                ->setWithCustomsInvoice(true)
                                ->setSigned(true)
                                ->save();
                        }

                        if (null !== $newStatus) {
                            $event = new OrderEvent($order);
                            $event->setStatus($newStatus->getId());

                            $this->dispatch(TheliaEvents::ORDER_UPDATE_STATUS, $event);
                        }

                        // Ajouter la facture au zip
                        $labelFileName = self::LABEL_DIRECTORY . DS . $order->getRef() . '-invoice.pdf';

                        $response = $this->generateOrderPdf($orderId, ConfigQuery::read('pdf_invoice_file', 'invoice'));

                        if (file_put_contents($labelFileName, $response->getContent())) {
                            $files[] = $labelFileName;
                        }
                    }
                }
            }

            if (count($files) > 0) {
                $zip = new \ZipArchive();
                $zipFilename = sys_get_temp_dir() .DS. uniqid('colissimo-labels-', false);

                if (true !== $zip->open($zipFilename, \ZipArchive::CREATE)) {
                    throw new TheliaProcessException("Cannot open zip file $zipFilename\n");
                }

                foreach ($files as $file) {
                    $zip->addFile($file, basename($file));
                }

                $zip->close();

                // Perform cleanup
                /*
                foreach ($files as $file) {
                    @unlink($file);
                }
                */

                $params = [ 'zip' => base64_encode($zipFilename) ];
            }
        } catch (\Exception $ex) {
            $this->setupFormErrorContext("Generation étiquettes Colissimo", $ex->getMessage(), $exportForm, $ex);
        }

        return $this->generateRedirect(URL::getInstance()->absoluteUrl("admin/module/ColissimoWs", $params));
    }

    /**
     * @param $orderId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getLabelZip($base64EncodedZipFilename)
    {
        $zipFilename = base64_decode($base64EncodedZipFilename);

        if (file_exists($zipFilename)) {
            return new StreamedResponse(
                function () use ($zipFilename) {
                    readfile($zipFilename);
                    @unlink($zipFilename);
                },
                200,
                [
                    'Content-Type' => 'application/zip',
                    "Content-disposition" => "attachement; filename=colissimo-labels.zip",
                    "Content-Length" => filesize($zipFilename)
                ]
            );
        }

        return new \Symfony\Component\HttpFoundation\Response("File no longer exists");
    }


    /**
     * @param $orderId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getLabel($orderId)
    {
        if (null !== $labelInfo = ColissimowsLabelQuery::create()->findOneByOrderId($orderId)) {
            return $this->generateResponseForLabel($labelInfo);
        }

        return $this->generateRedirect(URL::getInstance()->absoluteUrl("admin/module/ColissimoWs"));
    }

    /**
     * @param $orderId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function getCustomsInvoice($orderId)
    {
        if (null !== $order = OrderQuery::create()->findPk($orderId)) {
            $fileName = $this->createCustomsInvoice($orderId, $order->getRef());

            return Response::create(
                file_get_contents($fileName),
                200,
                [
                    "Content-Type" => "application/pdf",
                    "Content-disposition" => "Attachement;filename=" . basename($fileName)
                ]
            );
        }

        return $this->generateRedirect(URL::getInstance()->absoluteUrl("admin/module/ColissimoWs"));
    }


    /**
     * @param $orderId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function clearLabel($orderId)
    {
        /** @var ColissimowsLabel $order */
        $order = ColissimowsLabelQuery::create()->filterByOrderId($orderId)->findOne();

        $orderRef = $order->getOrderRef();
        $fileType = $order->getLabelType();
        $order->delete();

        $file = self::LABEL_DIRECTORY . DS . $orderRef;
        $invoice = $file . '-invoice.pdf';
        $file .= ".$fileType";
        @unlink($file);
        @unlink($invoice);

        ///** Compatibility with module SoColissimoLabel /!\ Do not use strict comparison */
        //if (ModuleQuery::create()->findOneByCode('ColissimoLabel')->getActivate() == true)
        //{
        //    ColissimoLabelQuery::create()->findOneByOrderId($orderId)->delete();
        //}

        return $this->generateRedirect(URL::getInstance()->absoluteUrl("admin/module/ColissimoWs") . '#order-' . $orderId);
    }

    /**
     * @param $orderId
     * @param $orderRef
     * @return string
     * @throws \Exception
     */
    public function createCustomsInvoice($orderId, $orderRef)
    {
        $html = $this->renderRaw(
            "customs-invoice",
            array(
                'order_id' => $orderId
            ),
            $this->getTemplateHelper()->getActivePdfTemplate()
        );

        try {
            $pdfEvent = new PdfEvent($html);

            $this->dispatch(TheliaEvents::GENERATE_PDF, $pdfEvent);

            $pdfFileName = self::LABEL_DIRECTORY . DS . $orderRef . '-customs-invoice.pdf';

            file_put_contents($pdfFileName, $pdfEvent->getPdf());

            return $pdfFileName;
        } catch (\Exception $e) {
            Tlog::getInstance()->error(
                sprintf(
                    'error during generating invoice pdf for order id : %d with message "%s"',
                    $orderId,
                    $e->getMessage()
                )
            );

            throw $e;
        }
    }

    /**
     * @param ColissimowsLabel $labelInfo
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function generateResponseForLabel($labelInfo)
    {
        $fileType = $labelInfo->getLabelType();

        if ($fileType === 'pdf') {
            return new BinaryFileResponse(
                self::LABEL_DIRECTORY . DS . $labelInfo->getOrderRef() . ".$fileType",
                200,
                [
                    "Content-Type" => "application/pdf",
                    "Content-disposition" => "Attachement;filename=" . $labelInfo->getOrder()->getRef() . ".pdf"
                ]
            );
        }

        return new BinaryFileResponse(
            self::LABEL_DIRECTORY . DS . $labelInfo->getOrderRef() . ".$fileType",
            200,
            [
                "Content-Type" => "application/octet-stream",
                "Content-disposition" => "Attachement;filename=" . $labelInfo->getOrder()->getRef() . ".$fileType"
            ]
        );
    }
}
