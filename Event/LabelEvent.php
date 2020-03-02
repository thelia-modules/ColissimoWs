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
 * Date: 04/09/2019 15:23
 */

namespace ColissimoWs\Event;

use ColissimoWs\Model\ColissimowsLabel;
use Thelia\Core\Event\ActionEvent;

class LabelEvent extends ActionEvent
{
    /** @var int */
    protected $orderId;

    /** @var ColissimowsLabel */
    protected $colissimoWsLabel = null;

    /** @var float|null */
    protected $weight = null;

    /** @var bool|null */
    protected $signed = null;
    /**
     * LabelEvent constructor.
     * @param int $orderId
     */
    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return ColissimowsLabel
     */
    public function getColissimoWsLabel()
    {
        return $this->colissimoWsLabel;
    }

    /**
     * @param ColissimowsLabel $colissimoWsLabel
     * @return $this
     */
    public function setColissimoWsLabel($colissimoWsLabel)
    {
        $this->colissimoWsLabel = $colissimoWsLabel;
        return $this;
    }

    public function hasLabel()
    {
        return null !== $this->colissimoWsLabel;
    }

    /**
     * @return float|null
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param float|null $weight
     * @return $this
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getSigned()
    {
        return $this->signed;
    }

    /**
     * @param bool|null $signed
     * @return $this
     */
    public function setSigned($signed)
    {
        $this->signed = $signed;
        return $this;
    }
}
