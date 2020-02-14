<?php

namespace modules\order\src\listeners\order;

use modules\order\src\entities\order\events\OrderRecalculateProfitAmountEvent;
use sales\helpers\app\AppHelper;
use sales\services\RecalculateProfitAmountService;
use Yii;

/**
 *
 * @property RecalculateProfitAmountService $recalculateProfitAmountService
 */
class OrderRecalculateProfitAmountListener
{
    private $recalculateProfitAmountService;

    /**
     * ProductQuoteRecalculateProfitAmountListener constructor.
     * @param RecalculateProfitAmountService $recalculateProfitAmountService
     */
    public function __construct(RecalculateProfitAmountService $recalculateProfitAmountService)
    {
        $this->recalculateProfitAmountService = $recalculateProfitAmountService;
    }

    /**
     * @param OrderRecalculateProfitAmountEvent $event
     */
    public function handle(OrderRecalculateProfitAmountEvent $event): void
    {
        try {
            $this->recalculateProfitAmountService->recalculateByOrder($event->orders);
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableFormatter($e), 'Listeners:' . self::class);
        }
    }
}