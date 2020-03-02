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
 * Date: 04/09/2019 17:53
 */
namespace ColissimoWs\Loop;

use ColissimoWs\ColissimoWs;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Core\Template\Loop\Order;
use Thelia\Model\OrderQuery;
use Thelia\Model\OrderStatus;
use Thelia\Model\OrderStatusQuery;

class OrdersNotYetSentLoop extends Order
{
    public function getArgDefinitions()
    {
        return new ArgumentCollection(Argument::createBooleanTypeArgument('with_prev_next_info', false));
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $status = OrderStatusQuery::create()
            ->filterByCode(
                array(
                    OrderStatus::CODE_PAID,
                    OrderStatus::CODE_PROCESSING,
                ),
                Criteria::IN
            )
            ->find()
            ->toArray("code");
        $query = OrderQuery::create()
            ->filterByDeliveryModuleId(ColissimoWs::getModCode())
            ->filterByStatusId(
                array(
                    $status[OrderStatus::CODE_PAID]['Id'],
                    $status[OrderStatus::CODE_PROCESSING]['Id']),
                Criteria::IN
            );

        return $query;
    }
}
