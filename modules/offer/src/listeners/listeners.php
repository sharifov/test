<?php

use modules\offer\src\entities\offer\events\OfferRecalculateProfitAmountEvent;
use modules\offer\src\listeners\offer\OfferRecalculateProfitAmountListener;

return [
    OfferRecalculateProfitAmountEvent::class => [OfferRecalculateProfitAmountListener::class],
];
