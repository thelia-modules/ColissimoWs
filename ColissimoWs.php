<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace ColissimoWs;

use ColissimoWs\Model\ColissimowsFreeshippingQuery;
use ColissimoWs\Model\ColissimowsPriceSlices;
use PDO;
use ColissimoWs\Model\ColissimowsLabelQuery;
use ColissimoWs\Model\ColissimowsPriceSlicesQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Propel;
use SoColissimo\Model\SocolissimoDeliveryModeQuery;
use Thelia\Model\ConfigQuery;
use Thelia\Model\Country;
use Thelia\Model\Message;
use Thelia\Model\MessageQuery;
use Thelia\Model\ModuleQuery;
use Thelia\Model\Order;
use Thelia\Module\AbstractDeliveryModule;
use Thelia\Module\BaseModule;
use Thelia\Module\DeliveryModuleInterface;
use Thelia\Module\Exception\DeliveryException;

class ColissimoWs extends AbstractDeliveryModule
{
    /** @var string */
    const DOMAIN_NAME = 'colissimows';

    // The shipping confirmation message identifier
    const CONFIRMATION_MESSAGE_NAME = 'order_confirmation_colissimows';

    // Events
    const GENERATE_LABEL_EVENT = 'colissimows.generate_label_event';
    const CLEAR_LABEL_EVENT = 'colissimows.clear_label_event';

    // Configuration parameters
    const COLISSIMO_USERNAME = 'colissimo_username';
    const COLISSIMO_PASSWORD = 'colissimo_password';
    const AFFRANCHISSEMENT_ENDPOINT_URL = 'affranchissement_endpoint_url';
    const FORMAT_ETIQUETTE = 'format_etiquette';
    const ACTIVATE_DETAILED_DEBUG = 'activate_detailed_debug';

    const FROM_NAME = 'company_name';
    const FROM_ADDRESS_1 = 'from_address_1';
    const FROM_ADDRESS_2 = 'from_address_2';
    const FROM_CITY = 'from_city';
    const FROM_ZIPCODE = 'from_zipcode';
    const FROM_COUNTRY = 'from_country';
    const FROM_CONTACT_EMAIL = 'from_contact_email';
    const FROM_PHONE = 'from_phone';

    /**
     * @param ConnectionInterface|null $con
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function postActivation(ConnectionInterface $con = null)
    {
        // Create table if required.
        try {
            ColissimowsLabelQuery::create()->findOne();
        } catch (\Exception $ex) {
            $database = new \Thelia\Install\Database($con->getWrappedConnection());
            $database->insertSql(null, [__DIR__ . "/Config/thelia.sql"]);

            self::setConfigValue(self::AFFRANCHISSEMENT_ENDPOINT_URL, 'https://ws.colissimo.fr/sls-ws/SlsServiceWS?wsdl');

            self::setConfigValue(ColissimoWs::FROM_NAME, ConfigQuery::getStoreName());
            self::setConfigValue(ColissimoWs::FROM_ADDRESS_1, ConfigQuery::read('store_address1'));
            self::setConfigValue(ColissimoWs::FROM_ADDRESS_2, ConfigQuery::read('store_address2'));
            self::setConfigValue(ColissimoWs::FROM_CITY, ConfigQuery::read('store_city'));
            self::setConfigValue(ColissimoWs::FROM_ZIPCODE, ConfigQuery::read('store_zipcode'));
            self::setConfigValue(ColissimoWs::FROM_CONTACT_EMAIL, ConfigQuery::read('store_email'));
            self::setConfigValue(ColissimoWs::FROM_COUNTRY, Country::getShopLocation()->getIsoalpha2());
            self::setConfigValue(ColissimoWs::FROM_PHONE, ConfigQuery::read('store_phone'));
        }

        if (null === MessageQuery::create()->findOneByName(self::CONFIRMATION_MESSAGE_NAME)) {
            $message = new Message();

            $message
                ->setName(self::CONFIRMATION_MESSAGE_NAME)
                ->setHtmlLayoutFileName('order_shipped.html')
                ->setTextLayoutFileName('order_shipped.txt')
                ->setLocale('en_US')
                ->setTitle('Order send confirmation')
                ->setSubject('Order send confirmation')

                ->setLocale('fr_FR')
                ->setTitle('Confirmation d\'envoi de commande')
                ->setSubject('Confirmation d\'envoi de commande')

                ->save()
            ;
        }


    }

    public static function getLabelFileType()
    {
        return strtolower(substr(ColissimoWs::getConfigValue(ColissimoWs::FORMAT_ETIQUETTE, 'PDF'), 0, 3));
    }

    /**
     * Returns ids of area containing this country and covers by this module
     * @param Country $country
     * @return array Area ids
     */
    private function getAllAreasForCountry(Country $country)
    {
        $areaArray = [];

        $sql = "SELECT ca.area_id as area_id FROM country_area ca
               INNER JOIN area_delivery_module adm ON (ca.area_id = adm.area_id AND adm.delivery_module_id = :p0)
               WHERE ca.country_id = :p1";

        $con = Propel::getConnection();

        $stmt = $con->prepare($sql);
        $stmt->bindValue(':p0', $this->getModuleModel()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':p1', $country->getId(), PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $areaArray[] = $row['area_id'];
        }

        return $areaArray;
    }

    /**
     * @param $areaId
     * @param $weight
     * @param $cartAmount
     * @param $deliverModeCode
     *
     * @return mixed
     * @throws DeliveryException
     */
    public static function getPostageAmount($areaId, $weight, $cartAmount = 0)
    {
        //@TODO : Handle Freeshipping (button activation sets a variable in module config ?)
        //$freeshipping = getFreeshippingActive();
        $freeshipping = false;

        //@TODO : Handle FreeshippingFrom
        //$freeshippingFrom = getFreeshippingFrom();
        $freeshippingFrom = null;

        //@TODO : Handle FreeShippingByArea (needs a dedicated function and probably a dedicated table too)


        $postage = 0;

        if (!$freeshipping) {
            $areaPrices = ColissimowsPriceSlicesQuery::create()
                ->filterByAreaId($areaId)
                ->filterByMaxWeight($weight, Criteria::GREATER_EQUAL)
                ->_or()
                ->filterByMaxWeight(null)
                ->filterByMaxPrice($cartAmount, Criteria::GREATER_EQUAL)
                ->_or()
                ->filterByMaxPrice(null)
                ->orderByMaxWeight()
                ->orderByMaxPrice()
            ;

            /** @var ColissimowsPriceSlices $firstPrice */
            $firstPrice = $areaPrices->find()
                ->getFirst();

            if (null === $firstPrice) {
                throw new DeliveryException("Colissimo delivery unavailable for your cart weight or delivery country");
            }

            //If a min price for freeshipping is defined and the cart amount reaches this value, return 0 (aka free shipping)
            if (null !== $freeshippingFrom && $freeshippingFrom <= $cartAmount) {
                $postage = 0;
                return $postage;
            }

            $postage = $firstPrice->getShipping();
        }
        return $postage;
    }

    private function getMinPostage($areaIdArray, $cartWeight, $cartAmount)
    {
        $minPostage = null;

        foreach ($areaIdArray as $areaId) {
            try {
                $postage = self::getPostageAmount($areaId, $cartWeight, $cartAmount);
                if ($minPostage === null || $postage < $minPostage) {
                    $minPostage = $postage;
                    if ($minPostage == 0) {
                        break;
                    }
                }
            } catch (\Exception $ex) {
            }
        }

        return $minPostage;
    }

    /**
     * Calculate and return delivery price
     *
     * @param  Country                          $country
     * @return mixed
     * @throws DeliveryException
     */
    public function getPostage(Country $country)
    {
        $request = $this->getRequest();

        $postage = 0;

        $freeshippingIsActive = ColissimowsFreeshippingQuery::create()->findOneById(1)->getActive();

        if (false === $freeshippingIsActive){
            $cartWeight = $request->getSession()->getSessionCart($this->getDispatcher())->getWeight();
            $cartAmount = $request->getSession()->getSessionCart($this->getDispatcher())->getTaxedAmount($country);

            $areaIdArray = $this->getAllAreasForCountry($country);
            if (empty($areaIdArray)) {
                throw new DeliveryException("Your delivery country is not covered by Colissimo.");
            }

            if (null === $postage = $this->getMinPostage($areaIdArray, $cartWeight, $cartAmount)) {
                throw new DeliveryException("Colissimo delivery unavailable for your cart weight or delivery country");
            }
        }

        return $postage;
    }

    /**
     * This method is called by the Delivery loop, to check if the current module has to be displayed to the customer.
     * Override it to implements your delivery rules/
     *
     * If you return true, the delivery method will de displayed to the customer
     * If you return false, the delivery method will not be displayed
     *
     * @param Country $country the country to deliver to.
     *
     * @return boolean
     */
    public function isValidDelivery(Country $country)
    {
        $areaId = $country->getAreaId();

        $prices = ColissimowsPriceSlicesQuery::create()
            ->filterByAreaId($areaId)
            ->findOne();



        /* check if Colissimo delivers the asked area*/
        if (null !== $prices) {
            return true;
        }
        return false;
    }

    public static function canOrderBeNotSigned(Order $order)
    {
        $areas = $order->getOrderAddressRelatedByDeliveryOrderAddressId()->getCountry()->getAreas();

        $areas_id = [];

        foreach ($areas as $area){
            $areas_id[] = $area->getId();
        }

        if (in_array(4, $areas_id) || in_array(5, $areas_id)) // If order's country isn't in Europe or in DOM-TOM so order has to be signed
            return false;
        else
            return true;
    }

    /**
     * @param Order $order
     * @return string
     * Get the area code for order (used to generate colissimoWs label with or without signature)
     * Codes :
     *      - FR : France
     *      - DT : Dom-Tom
     *      - EU : Europe
     *      - WO : World
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public static function getOrderShippingArea(Order $order){
        $areas = $order->getOrderAddressRelatedByDeliveryOrderAddressId()->getCountry()->getAreas();

        $areas_id = [];

        foreach ($areas as $area){
            $areas_id[] = $area->getId();
        }

        if (in_array(1, $areas_id)){
            return 'FR';
        }

        if (in_array(2, $areas_id) || in_array(3, $areas_id)){
            return 'EU';
        }

        if (in_array(4, $areas_id) || in_array(5, $areas_id)){
            return 'WO';
        }

        if (in_array(6, $areas_id)){
            return 'DT';
        }

        return null;
    }

    public static function getModCode()
    {
        return ModuleQuery::create()->findOneByCode("ColissimoWs")->getId();
    }
}
