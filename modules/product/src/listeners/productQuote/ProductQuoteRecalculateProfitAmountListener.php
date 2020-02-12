<?php

namespace modules\product\src\listeners\productQuote;

use modules\product\src\entities\productQuote\events\ProductQuoteRecalculateProfitAmountEvent;
use modules\product\src\services\RecalculateProfitAmountService;
use sales\helpers\app\AppHelper;
use Yii;

/**
 *
 * @property RecalculateProfitAmountService $recalculateProfitAmountService
 */
class ProductQuoteRecalculateProfitAmountListener
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
     * @param ProductQuoteRecalculateProfitAmountEvent $event
     */
    public function handle(ProductQuoteRecalculateProfitAmountEvent $event): void
    {
        try {
            $this->recalculateProfitAmountService->recalculateByProductQuote($event->productQuoteId);
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableFormatter($e), 'Listeners:' . self::class);
        }
    }
}