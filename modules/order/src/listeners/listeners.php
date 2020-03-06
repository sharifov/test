<?php

use modules\order\src\entities\order\events\OrderRecalculateProfitAmountEvent;
use modules\order\src\entities\order\events\OrderUserProfitUpdateProfitAmountEvent;
use modules\order\src\entities\order\events\UpdateOrderTipsUserProfitAmountEvent;
use modules\order\src\listeners\order\OrderRecalculateProfitAmountListener;
use modules\order\src\listeners\orderTipsUserProfit\UpdateOrderTipsUserProfitAmountEventListener;
use modules\order\src\listeners\orderUserProfit\OrderUserProfitUpdateProfitAmountEventListener;

return [
    OrderRecalculateProfitAmountEvent::class => [OrderRecalculateProfitAmountListener::class],
	OrderUserProfitUpdateProfitAmountEvent::class => [OrderUserProfitUpdateProfitAmountEventListener::class],
];
