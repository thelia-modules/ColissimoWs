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
 * Date: 04/09/2019 14:34
 */

namespace ColissimoWs\EventListeners;

use ColissimoLabel\ColissimoLabel;
use ColissimoLabel\Model\ColissimoLabelQuery;
use ColissimoPostage\ServiceType\Generate;
use ColissimoPostage\StructType\Address;
use ColissimoPostage\StructType\Addressee;
use ColissimoPostage\StructType\Article;
use ColissimoPostage\StructType\Category;
use ColissimoPostage\StructType\Contents;
use ColissimoPostage\StructType\CustomsDeclarations;
use ColissimoPostage\StructType\GenerateLabel;
use ColissimoPostage\StructType\GenerateLabelRequest;
use ColissimoPostage\StructType\Letter;
use ColissimoPostage\StructType\OutputFormat;
use ColissimoPostage\StructType\Parcel;
use ColissimoPostage\StructType\Sender;
use ColissimoPostage\StructType\Service;
use ColissimoWs\ColissimoWs;
use ColissimoWs\Event\LabelEvent;
use ColissimoWs\Model\ColissimowsLabel;
use ColissimoWs\Model\ColissimowsLabelQuery;
use ColissimoWs\Soap\GenerateWithAttachments;
use ColissimoWs\Soap\SoapClientWithAttachements;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Action\BaseAction;
use Thelia\Log\Tlog;
use Thelia\Model\ConfigQuery;
use Thelia\Model\Country;
use Thelia\Model\ModuleQuery;
use Thelia\Model\OrderProduct;
use Thelia\Model\OrderQuery;
use Thelia\Tools\MoneyFormat;
use WsdlToPhp\PackageBase\AbstractSoapClientBase;

class ShippingLabelGenerator extends BaseAction implements EventSubscriberInterface
{
    /** @var array  */
    protected $options;

    /** @var Generate */
    protected $generate;

    /** @var RequestStack */
    protected $requestStack;

    /** @var bool */
    protected $verbose;

    /**
     * ShippingLabelGenerator constructor.
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->options = [
            AbstractSoapClientBase::WSDL_URL => ColissimoWs::getConfigValue(ColissimoWs::AFFRANCHISSEMENT_ENDPOINT_URL),
            AbstractSoapClientBase::WSDL_CLASSMAP => \ColissimoPostage\ClassMap::get(),
        ];

        // Générer les services
        $this->generate = new GenerateWithAttachments($this->options);

        $this->requestStack = $requestStack;

        $this->verbose = 0 !== (int) ColissimoWs::getConfigValue(ColissimoWs::ACTIVATE_DETAILED_DEBUG, 0);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            ColissimoWs::GENERATE_LABEL_EVENT => [ 'generateShippingLabel', 128 ],
            ColissimoWs::CLEAR_LABEL_EVENT =>  [ 'clearShippingLabel', 128 ],
        ];
    }


    /**
     * Clear a label
     *
     * @param LabelEvent $event
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function clearShippingLabel(LabelEvent $event)
    {
        ColissimowsLabelQuery::create()
            ->filterByOrderId($event->getOrderId())
            ->delete();
    }

    /**
     * Create a new label from the order
     *
     * @param LabelEvent $event
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function generateShippingLabel(LabelEvent $event)
    {
        $erreur = false;

        $trackingNumber = $labelContent = $message = '';

        $request = $this->requestStack->getCurrentRequest();

        $customsDeclaration = null;

        if (null !== $order = OrderQuery::create()->findPk($event->getOrderId())) {
            $totalWeight = 0;

            $signed = $event->getSigned();

            $shopCountryCode = strtoupper(Country::getShopLocation()->getIsoalpha2());

            $articles = [];

            /** @var OrderProduct $orderProduct */
            foreach ($order->getOrderProducts() as $orderProduct) {
                $totalWeight += $orderProduct->getQuantity() * $orderProduct->getWeight();

                $articles[] = new Article(
                    $orderProduct->getTitle(),
                    $orderProduct->getQuantity(),
                    $orderProduct->getWeight(),
                    MoneyFormat::getInstance($request)->formatStandardMoney($orderProduct->getPrice()),
                    '3303001000',
                    $shopCountryCode,
                    $order->getCurrency()->getCode(),
                    null,
                    null
                );
            }

            $customer = $order->getCustomer();
            $defaultAddress = $customer->getDefaultAddress();
            $deliveryAddress = $order->getOrderAddressRelatedByDeliveryOrderAddressId();

            $mobilePhone = $this->formatterTelephone($defaultAddress->getCellphone(), $defaultAddress->getCountry()->getIsoalpha2());
            $landPhone = $this->formatterTelephone($defaultAddress->getPhone(), $defaultAddress->getCountry()->getIsoalpha2());

            $storePhone = $this->formatterTelephone(
                ColissimoWs::getConfigValue(ColissimoWs::FROM_PHONE, ConfigQuery::read('store_phone')),
                ColissimoWs::getConfigValue(ColissimoWs::FROM_COUNTRY, Country::getShopLocation()->getIsoalpha2())
            );

            switch(ColissimoWs::getOrderShippingArea($order)){
                case 'FR':
                    if($signed) {
                        $colissimoProductCode = "DOS";
                    } else {
                        $colissimoProductCode = "DOM";
                    }

                    break;

                case 'EU':
                    if($signed) {
                        $colissimoProductCode = "DOS";
                    } else {
                        $colissimoProductCode = "COLI";
                    }
                    $customsDeclaration = new CustomsDeclarations(
                        true,
                        new Contents(
                            $articles,
                            new Category(3)
                        )
                    );
                    break;

                case 'WO':
                    $colissimoProductCode = "COLI";

                    $customsDeclaration = new CustomsDeclarations(
                        true,
                        new Contents(
                            $articles,
                            new Category(3)
                        )
                    );
                    break;

                case 'DT':
                    if($signed) {
                        $colissimoProductCode = "CDS";
                    } else {
                        $colissimoProductCode = "COM";
                    }

                    $customsDeclaration = new CustomsDeclarations(
                        true,
                        new Contents(
                            $articles,
                            new Category(3)
                        )
                    );
                    break;

                default:
                    throw new \InvalidArgumentException("Failed to find order area " . $event->getOrderId());
                    break;
            }


            // Use provided weight if any.
            if (!empty($event->getWeight())) {
                // The specified weight cannot be less than the total articles weight
                if ($event->getWeight() > $totalWeight) {
                    $totalWeight = $event->getWeight();
                }
            }

            // Envoyer la requête
            $success = $this->generate->generateLabel(
                new GenerateLabel(
                    new GenerateLabelRequest(
                        ColissimoWs::getConfigValue(ColissimoWs::COLISSIMO_USERNAME),
                        ColissimoWs::getConfigValue(ColissimoWs::COLISSIMO_PASSWORD),

                        new OutputFormat(0, 0, ColissimoWs::getConfigValue(ColissimoWs::FORMAT_ETIQUETTE, 'PDF_10x15_300dpi'), true, ''),
                        new Letter(
                            new Service(
                                $colissimoProductCode,
                                date('Y-m-d', strtotime('tomorrow')),
                                false,
                                null,
                                null,
                                null,
                                null,
                                null,
                                round(100 * MoneyFormat::getInstance($request)->formatStandardMoney($order->getTotalAmount())),
                                $order->getRef(),
                                ConfigQuery::getStoreName(),
                                3 // Ne pas retourner
                            ),
                            new Parcel(
                                null,
                                null,
                                null,
                                null,
                                $totalWeight,
                                false,
                                null,
                                null,
                                null,
                                false,
                                null,
                                null
                            ),
                            $customsDeclaration,
                            new Sender(
                                $order->getRef(),
                                new Address(
                                    ColissimoWs::getConfigValue(ColissimoWs::FROM_NAME, ConfigQuery::getStoreName()),
                                    null,
                                    null,
                                    null,
                                    null,
                                    ColissimoWs::getConfigValue(ColissimoWs::FROM_ADDRESS_1, ConfigQuery::read('store_address1')),
                                    ColissimoWs::getConfigValue(ColissimoWs::FROM_ADDRESS_2, ConfigQuery::read('store_address2')),
                                    'FR',
                                    ColissimoWs::getConfigValue(ColissimoWs::FROM_CITY, ConfigQuery::read('store_city')),
                                    ColissimoWs::getConfigValue(ColissimoWs::FROM_ZIPCODE, ConfigQuery::read('store_zipcode')),
                                    $storePhone,
                                    null,
                                    null,
                                    null,
                                    ColissimoWs::getConfigValue(ColissimoWs::FROM_CONTACT_EMAIL, ConfigQuery::read('store_email')),
                                    null,
                                    strtoupper(ColissimoWs::getConfigValue(ColissimoWs::FROM_COUNTRY, Country::getShopLocation()->getIsoalpha2()))
                                )
                            ),
                            new Addressee(
                                $customer->getRef(),
                                false,
                                null,
                                null,
                                new Address(
                                    '',
                                    $this->cleanUpAddresse($deliveryAddress->getFirstname()),
                                    $this->cleanUpAddresse($deliveryAddress->getLastname()),
                                    null,
                                    null,
                                    $this->cleanUpAddresse($deliveryAddress->getAddress1()),
                                    $this->cleanUpAddresse($deliveryAddress->getAddress2()),
                                    strtoupper($deliveryAddress->getCountry()->getIsoalpha2()),
                                    $this->corrigerLocalite($deliveryAddress->getCity()),
                                    preg_replace("/[\s]/", "", $deliveryAddress->getZipcode()),
                                    $landPhone,
                                    $mobilePhone,
                                    null,
                                    null,
                                    $customer->getEmail(),
                                    null,
                                    strtoupper($customer->getCustomerLang()->getCode()))
                            )
                        )
                    )
                )
            );

            Tlog::getInstance()->debug("Colissimo shipping label request: " . $this->generate->getLastRequest());

            /*
                echo "<pre>";
                echo 'XML Request: ' . htmlspecialchars($this->generate->getLastRequest()) . "\r\n";
                echo 'Headers Request: ' . htmlspecialchars($this->generate->getLastRequestHeaders()) . "\r\n";
                echo 'XML Response: ' . htmlspecialchars($this->generate->getLastResponse()) . "\r\n";
                echo 'Headers Response: ' . htmlspecialchars($this->generate->getLastResponseHeaders()) . "\r\n";
                echo "</pre>";
            */
            if ($this->generate->getLastResponse(true) instanceof \DOMDocument) {
                $response = $this->generate->getLastResponse();
                Tlog::getInstance()->debug("Colissimo shipping label response: " . $response);

                echo $response;

                $domDocument = $this->generate->getLastResponse(true);

                $type = $domDocument->getElementsByTagName('type')->item(0)->nodeValue;

                if ($type !== 'ERROR') {
                    $pdfUrlElement = $domDocument->getElementsByTagName('pdfUrl');
                    $includeElement = $domDocument->getElementsByTagName('Include');

                    if ($pdfUrlElement->length > 0) {
                        $urlPdf = $pdfUrlElement->item(0)->nodeValue;

                        if (!empty($urlPdf)) {
                            $labelContent = file_get_contents($urlPdf);
                        }
                    } elseif ($includeElement->length > 0) {
                        $href = str_replace('cid:', '', $includeElement->item(0)->attributes['href']->value);

                        $rawResponse = $this->generate->getRawResponse();

                        preg_match("/.*Content-ID: <$href>(.*)--uuid.*$/s", $rawResponse, $matches);

                        if (isset($matches[1])) {
                            $labelContent = trim($matches[1]);
                        }
                    }

                    $trackingNumber = $domDocument->getElementsByTagName('parcelNumber')->item(0)->nodeValue;

                    // Update tracking number.
                    $order
                        ->setDeliveryRef($trackingNumber)
                        ->save();

                    $message = "L'étiquette a été générée correctement.";
                } else {
                    $erreur = true;
                    $message = $domDocument->getElementsByTagName('messageContent')->item(0)->nodeValue;
                }
            } else {
                $erreur = true;
                $message = $this->generate->getLastErrorForMethod('ColissimoPostage\ServiceType\Generate::generateLabel')->getMessage();
            }

            if (null === $label = ColissimowsLabelQuery::create()->findOneByOrderId($order->getId())) {
                $label = (new ColissimowsLabel())
                    ->setOrderId($order->getId())
                    ->setOrderRef($order->getRef())
                    ;
            }

            $label
                ->setError($erreur)
                ->setErrorMessage($message)
                ->setTrackingNumber($trackingNumber)
                ->setLabelData($labelContent)
                ->setLabelType(ColissimoWs::getLabelFileType())
                ->setWeight($totalWeight)
                ->setSigned($signed)
            ;

            $label ->save();

            ///** Compatibility with module SoColissimoLabel /!\ Do not use strict comparison */
            //if (ModuleQuery::create()->findOneByCode('ColissimoLabel')->getActivate() == true)
            //{
            //    if (null === $labelCompat = ColissimoLabelQuery::create()->findOneByOrderId($order->getId())) {
            //        /** @var  $labelCompat */
            //        $labelCompat = (new \ColissimoLabel\Model\ColissimoLabel())
            //            ->setOrderId($order->getId())
            //        ;
            //    }
//
            //    $labelCompat
            //        ->setWeight($totalWeight)
            //        ->setSigned($signed)
            //        ->setNumber($trackingNumber)
            //        ;
//
            //    $labelCompat->save();
            //}

            $event->setColissimoWsLabel($label);
        } else {
            throw new \InvalidArgumentException("Failed to find order ID " . $event->getOrderId());
        }
    }

    protected function formatterTelephone($numero, $codePays)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            $normalizedNumber = $phoneUtil->parse($numero, strtoupper($codePays));

            return $phoneUtil->format($normalizedNumber, \libphonenumber\PhoneNumberFormat::E164);
        } catch (NumberParseException $e) {
            return '';
        }
    }

    protected function corrigerLocalite($localite)
    {
        $localite = strtoupper($localite);

        $localite = str_replace(['SAINTE', 'SAINT', '/'], array('STE', 'ST', ''), $localite);

        return $localite;
    }

    protected function cleanUpAddresse($str)
    {
        return preg_replace("/[^A-Za-z0-9]/", ' ', $this->removeAccents($str));
    }

    protected function cleanupUserEnteredString($str)
    {
        $str = preg_replace("/&#[0-9]+;/", '', $str);
        $str = preg_replace("/[^A-Za-z0-9]/", ' ', $this->removeAccents($str));

        return $str;
    }

    protected function removeAccents($str)
    {
        return \Transliterator::create('NFD; [:Nonspacing Mark:] Remove; NFC')->transliterate($str);
    }
}
