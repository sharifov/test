<?php

namespace modules\offer\src\entities\offer\events;

/**
 * Class OfferRecalculateProfitAmountEvent
 * @package modules\offer\src\entities\offer\events
 */
class OfferRecalculateProfitAmountEvent
{
    public $offers;

    /**
     * OfferRecalculateProfitAmountEvent constructor.
     * @param array $offers [Offer]
     */
    public function __construct(array $offers)
    {
        $this->offers = $offers;
    }
}
