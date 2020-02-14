<?php

use modules\order\src\entities\order\events\OrderRecalculateProfitAmountEvent;
use modules\order\src\listeners\order\OrderRecalculateProfitAmountListener;

return [
    OrderRecalculateProfitAmountEvent::class => [OrderRecalculateProfitAmountListener::class],
];
