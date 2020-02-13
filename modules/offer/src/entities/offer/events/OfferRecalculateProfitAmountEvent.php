<?php

namespace modules\offer\src\entities\offer\events;

use modules\offer\src\entities\offer\Offer;

/**
 * Class OfferRecalculateProfitAmountEvent
 * @package modules\offer\src\entities\offer\events
 */
class OfferRecalculateProfitAmountEvent
{
    public $offer;

    /**
     * OfferRecalculateProfitAmountEvent constructor.
     * @param Offer $offer
     */
    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
    }
}
