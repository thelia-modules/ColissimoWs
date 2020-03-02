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
 * Date: 04/09/2019 17:56
 */
namespace ColissimoWs\Loop;

use ColissimoWs\ColissimoWs;
use ColissimoWs\Model\ColissimowsLabel;
use ColissimoWs\Model\ColissimowsLabelQuery;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Model\OrderQuery;
use Thelia\Tools\URL;

/**
 * @package SimpleDhl\Loop
 * @method int getOrderId()
 */
class ColissimoWsLabelInfo extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntTypeArgument('order_id', null, true)
        );
    }

    public function buildModelCriteria()
    {
        return ColissimowsLabelQuery::create()
            ->filterByOrderId($this->getOrderId());
    }

    /**
     * @param LoopResult $loopResult
     * @return LoopResult
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function parseResults(LoopResult $loopResult)
    {
        if ($loopResult->getResultDataCollectionCount() === 0) {
            if (null !== $order = OrderQuery::create()->findPk($this->getOrderId())) {
                $loopResultRow = new LoopResultRow();

                $loopResultRow
                    ->set("ORDER_ID", $this->getOrderId())
                    ->set("HAS_ERROR", false)
                    ->set("ERROR_MESSAGE", null)
                    ->set("WEIGHT", $order->getWeight())
                    ->set("SIGNED", true)
                    ->set("TRACKING_NUMBER", null)
                    ->set("HAS_LABEL", false)
                    ->set("LABEL_URL", URL::getInstance()->absoluteUrl("/admin/module/colissimows/label/" . $this->getOrderId()))
                    ->set("CLEAR_LABEL_URL", URL::getInstance()->absoluteUrl("/admin/module/colissimows/label/clear/" . $this->getOrderId()))
                    ->set("CAN_BE_NOT_SIGNED", ColissimoWs::canOrderBeNotSigned($order));

                $loopResult->addRow($loopResultRow);
            }
        } else {
            /** @var ColissimowsLabel $result */
            foreach ($loopResult->getResultDataCollection() as $result) {
                $loopResultRow = new LoopResultRow();

                $loopResultRow
                    ->set("ORDER_ID", $result->getOrderId())
                    ->set("HAS_ERROR", $result->getError())
                    ->set("ERROR_MESSAGE", $result->getErrorMessage())
                    ->set("WEIGHT", empty($result->getWeight()) ? $result->getOrder()->getWeight() : $result->getWeight())
                    ->set("SIGNED", $result->getSigned())
                    ->set("TRACKING_NUMBER", $result->getTrackingNumber())
                    ->set("HAS_LABEL", ! empty($result->getLabelData()))
                    ->set("LABEL_TYPE", $result->getLabelType())
                    ->set("HAS_CUSTOMS_INVOICE", $result->getWithCustomsInvoice())
                    ->set("LABEL_URL", URL::getInstance()->absoluteUrl("/admin/module/colissimows/label/" . $result->getOrderId()))
                    ->set("CUSTOMS_INVOICE_URL", URL::getInstance()->absoluteUrl("/admin/module/colissimows/customs-invoice/" . $result->getOrderId()))
                    ->set("CLEAR_LABEL_URL", URL::getInstance()->absoluteUrl("/admin/module/colissimows/label/clear/" . $result->getOrderId()))
                    ->set("CAN_BE_NOT_SIGNED", ColissimoWs::canOrderBeNotSigned($result->getOrder()));

                $loopResult->addRow($loopResultRow);
            }
        }

        return $loopResult;
    }
}
