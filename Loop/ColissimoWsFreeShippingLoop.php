<?php

namespace ColissimoWs\Loop;

use ColissimoWs\Model\ColissimowsFreeshipping;
use ColissimoWs\Model\ColissimowsFreeshippingQuery;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

class ColissimoWsFreeShippingLoop extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntTypeArgument('id')
        );
    }

    public function buildModelCriteria()
    {
        if (null === $isFreeShippingActive = ColissimowsFreeshippingQuery::create()->findOneById(1)){
            $isFreeShippingActive = new ColissimowsFreeshipping();
            $isFreeShippingActive->setId(1);
            $isFreeShippingActive->setActive(0);
            $isFreeShippingActive->save();
        }

        return ColissimowsFreeshippingQuery::create()->filterById(1);
    }

    public function parseResults(LoopResult $loopResult)
    {
        /** @var \ColissimoWs\Model\ColissimowsFreeshipping $freeshipping */
        foreach ($loopResult->getResultDataCollection() as $freeshipping) {
            $loopResultRow = new LoopResultRow($freeshipping);
            $loopResultRow
                ->set("FREESHIPPING_ACTIVE", $freeshipping->getActive());
            $loopResult->addRow($loopResultRow);
        }
        return $loopResult;
    }

}