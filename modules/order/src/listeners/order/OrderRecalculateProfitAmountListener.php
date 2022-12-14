<?php

namespace modules\order\src\listeners\order;

use modules\order\src\entities\order\events\OrderRecalculateProfitAmountEvent;
use src\helpers\app\AppHelper;
use src\services\RecalculateProfitAmountService;
use Yii;
use yii\helpers\VarDumper;

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
            $this->recalculateProfitAmountService->setOrders($event->orders)->recalculateOrders();
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableFormatter($e), 'Listeners:' . self::class);
        }
    }
}
