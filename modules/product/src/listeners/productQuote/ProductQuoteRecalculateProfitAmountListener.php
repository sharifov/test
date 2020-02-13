<?php

namespace modules\product\src\listeners\productQuote;

use modules\product\src\entities\productQuote\events\ProductQuoteRecalculateProfitAmountEvent;
use sales\helpers\app\AppHelper;
use sales\services\RecalculateProfitAmountService;
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
            $this->recalculateProfitAmountService->recalculate($event->productQuoteId);

            if ($this->recalculateProfitAmountService->changedOffers) {
                $this->recalculateProfitAmountService->saveOffers();
            }
            if ($this->recalculateProfitAmountService->changedOrders) {
                $this->recalculateProfitAmountService->saveOrders();
            }
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableFormatter($e), 'Listeners:' . self::class);
        }
    }
}