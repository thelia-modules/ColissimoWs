<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia                                                                       */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

namespace ColissimoWs\Controller;

use ColissimoWs\Form\FreeShippingForm;
use ColissimoWs\Model\ColissimowsAreaFreeshippingQuery;
use ColissimoWs\Model\ColissimowsFreeshipping;
use ColissimoWs\Model\ColissimowsFreeshippingQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Thelia\Controller\Admin\BaseAdminController;

use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Security\AccessManager;
use Thelia\Model\AreaQuery;
use Thelia\Tools\URL;

class FreeShippingController extends BaseAdminController
{
    public function toggleFreeShippingActivation()
    {
        if (null !== $response = $this
                ->checkAuth(array(AdminResources::MODULE), array('ColissimoWs'), AccessManager::UPDATE)) {
            return $response;
        }

        $form = new FreeShippingForm($this->getRequest());
        $response = null;

        try {
            $vform = $this->validateForm($form);
            $freeshipping = $vform->get('freeshipping')->getData();
            $freeshippingFrom = $vform->get('freeshipping_from')->getData();

            if (null === $isFreeShippingActive = ColissimowsFreeshippingQuery::create()->findOneById(1)){
                $isFreeShippingActive = new ColissimowsFreeshipping();
            }

            $isFreeShippingActive
                ->setActive($freeshipping)
                ->setFreeshippingFrom($freeshippingFrom)
            ;
            $isFreeShippingActive->save();

            $response = $this->generateRedirect(URL::getInstance()->absoluteUrl("/admin/module/ColissimoWs"));
        } catch (\Exception $e) {
        }
        return $response;
    }

    /**
     * @return mixed|\Symfony\Component\HttpFoundation\Response|null
     */
    public function setAreaFreeShipping()
    {
        if (null !== $response = $this
                ->checkAuth(array(AdminResources::MODULE), array('ColissimoWs'), AccessManager::UPDATE)) {
            return $response;
        }

        try {
            $data = $this->getRequest()->request;

            $colissimows_area_id = $data->get('area-id');
            $cartAmount = $data->get('cart-amount');

            if ($cartAmount < 0 || $cartAmount === '') {
                $cartAmount = null;
            }

            $areaQuery = AreaQuery::create()->findOneById($colissimows_area_id);
            if (null === $areaQuery) {
                return null;
            }

            $colissimowsAreaFreeshippingQuery = ColissimowsAreaFreeshippingQuery::create()
                ->filterByAreaId($colissimows_area_id)
                ->findOneOrCreate();

            $colissimowsAreaFreeshippingQuery
                ->setAreaId($colissimows_area_id)
                ->setCartAmount($cartAmount)
                ->save();

        } catch (\Exception $e) {
        }

        return $this->generateRedirect(URL::getInstance()->absoluteUrl('/admin/module/ColissimoWs'));
    }

}
