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
    const LABEL_DIRECTORY = THELIA_LOCAL_DIR . 'colissimo-label';

    /**
     * [DEPRECATED] Generates the customs invoice.
     * /!\ COMPATIBILITY /!\ DO NOT REMOVE
     *
     *
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
}
