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
use ColissimoWs\Model\ColissimowsFreeshipping;
use ColissimoWs\Model\ColissimowsFreeshippingQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Thelia\Controller\Admin\BaseAdminController;

use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Security\AccessManager;

class FreeShippingController extends BaseAdminController
{
    public function toggleFreeShippingActivation()
    {
        if (null !== $response = $this
                ->checkAuth(array(AdminResources::MODULE), array('ColissimoWs'), AccessManager::UPDATE)) {
            return $response;
        }

        $form = new FreeShippingForm($this->getRequest());
        $response=null;

        try {
            $vform = $this->validateForm($form);
            $freeshipping = $vform->get('freeshipping')->getData();

            if (null === $isFreeShippingActive = ColissimowsFreeshippingQuery::create()->findOneById(1)){
                $isFreeShippingActive = new ColissimowsFreeshipping();
            }

            $isFreeShippingActive->setActive($freeshipping);

            $isFreeShippingActive->save();

            $response = JsonResponse::create(array("success"=>"Freeshipping activated"), 200);
        } catch (\Exception $e) {
            $response = JsonResponse::create(array("error"=>$e->getMessage()), 500);
        }
        return $response;
    }
}
