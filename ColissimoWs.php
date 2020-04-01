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

use ColissimoWs\Model\ColissimowsAreaFreeshippingQuery;
use ColissimoWs\Model\ColissimowsFreeshippingQuery;
use ColissimoWs\Model\ColissimowsPriceSlices;
use PDO;
use ColissimoWs\Model\ColissimowsPriceSlicesQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Propel;
use Symfony\Component\Finder\Finder;
use Thelia\Install\Database;
use Thelia\Model\ConfigQuery;
use Thelia\Model\Country;
use Thelia\Model\Message;
use Thelia\Model\MessageQuery;
use Thelia\Model\ModuleQuery;
use Thelia\Module\AbstractDeliveryModule;
use Thelia\Module\Exception\DeliveryException;

class ColissimoWs extends AbstractDeliveryModule
{
    /** @var string */
    const DOMAIN_NAME = 'colissimows';

    // The shipping confirmation message identifier
    const CONFIRMATION_MESSAGE_NAME = 'order_confirmation_colissimows';

    // Configuration parameters
    const COLISSIMO_USERNAME = 'colissimo_username';
    const COLISSIMO_PASSWORD = 'colissimo_password';
    const AFFRANCHISSEMENT_ENDPOINT_URL = 'affranchissement_endpoint_url';
    const ACTIVATE_DETAILED_DEBUG = 'activate_detailed_debug';

    /**
     * @param ConnectionInterface|null $con
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function postActivation(ConnectionInterface $con = null)
    {
        // Create table if required.
        try {
            ColissimowsPriceSlicesQuery::create()->findOne();
            ColissimowsFreeshippingQuery::create()->findOne();
            ColissimowsAreaFreeshippingQuery::create()->findOne();
        } catch (\Exception $ex) {
            $database = new Database($con->getWrappedConnection());
            $database->insertSql(null, [__DIR__ . "/Config/thelia.sql"]);
        }

        if (!ColissimowsFreeshippingQuery::create()->filterById(1)->findOne()) {
            ColissimowsFreeshippingQuery::create()->filterById(1)->findOneOrCreate()->setActive(0)->save();
        }

        if (!self::getConfigValue(self::AFFRANCHISSEMENT_ENDPOINT_URL)) {
            self::setConfigValue(self::AFFRANCHISSEMENT_ENDPOINT_URL, 'https://ws.colissimo.fr/sls-ws/SlsServiceWS?wsdl');
        }

        if (!self::getConfigValue(self::COLISSIMO_USERNAME)) {
            self::setConfigValue(self::COLISSIMO_USERNAME, ' ');
        }

        if (!self::getConfigValue(self::COLISSIMO_PASSWORD)) {
            self::setConfigValue(self::COLISSIMO_PASSWORD, ' ');
        }

        if (!self::getConfigValue(self::ACTIVATE_DETAILED_DEBUG)) {
            self::setConfigValue(self::ACTIVATE_DETAILED_DEBUG, '0');
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

    /**
     * @inheritDoc
     */
    public function update($currentVersion, $newVersion, ConnectionInterface $con = null)
    {
        $finder = (new Finder)
            ->files()
            ->name('#.*?\.sql#')
            ->sortByName()
            ->in(__DIR__ . DS . 'Config' . DS . 'update');

        $database = new Database($con);

        /** @var \Symfony\Component\Finder\SplFileInfo $updateSQLFile */
        foreach ($finder as $updateSQLFile) {
            if (version_compare($currentVersion, str_replace('.sql', '', $updateSQLFile->getFilename()), '<')) {
                $database->insertSql(
                    null,
                    [
                        $updateSQLFile->getPathname()
                    ]
                );
            }
        }
    }

    /**
     * Returns ids of area containing this country and covers by this module
     * @param Country $country
     * @return array Area ids
     */
    private function getAllAreasForCountry(Country $country)
    {
        $areaArray = [];

        $sql = 'SELECT ca.area_id as area_id FROM country_area ca
               INNER JOIN area_delivery_module adm ON (ca.area_id = adm.area_id AND adm.delivery_module_id = :p0)
               WHERE ca.country_id = :p1';

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
        $freeshippingFrom = ColissimowsFreeshippingQuery::create()->findOneById(1)->getFreeshippingFrom();
        $areaFreeshipping = ColissimowsAreaFreeshippingQuery::create()->findOneByAreaId($areaId);

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

        /** If a min price for freeshippingFrom is defined and the cart amount reaches this value, return 0 (aka free shipping) */
        if (null !== $freeshippingFrom && $freeshippingFrom <= $cartAmount) {
            $postage = 0;
            return $postage;
        }

        /** If a min price for areaFreeshipping is defined and the cart amount reaches this value, return 0 (aka free shipping) */
        if (null !== $areaFreeshipping->getCartAmount() && $areaFreeshipping->getCartAmount() <= $cartAmount) {
            $postage = 0;
            return $postage;
        }

        $postage = $firstPrice->getShipping();

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

    public static function getModCode()
    {
        return ModuleQuery::create()->findOneByCode("ColissimoWs")->getId();
    }
}
