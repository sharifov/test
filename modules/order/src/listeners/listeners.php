<?php

use modules\order\src\entities\order\events\OrderRecalculateProfitAmountEvent;

return [
    OrderRecalculateProfitAmountEvent::class => [OrderRecalculateProfitAmountListener::class],
];
