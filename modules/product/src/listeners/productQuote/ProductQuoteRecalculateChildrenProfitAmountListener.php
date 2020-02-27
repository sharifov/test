<?php

namespace modules\product\src\listeners\productQuote;

use modules\product\src\entities\productQuote\events\ProductQuoteRecalculateChildrenProfitAmountEvent;
use sales\helpers\app\AppHelper;
use sales\services\RecalculateProfitAmountService;
use Yii;

/**
 *
 * @property RecalculateProfitAmountService $recalculateProfitAmountService
 */
class ProductQuoteRecalculateChildrenProfitAmountListener
{
    private $recalculateProfitAmountService;

    /**
     * ProductQuoteRecalculateChildrenProfitAmountListener constructor.
     * @param RecalculateProfitAmountService $recalculateProfitAmountService
     */
    public function __construct(RecalculateProfitAmountService $recalculateProfitAmountService)
    {
        $this->recalculateProfitAmountService = $recalculateProfitAmountService;
    }

    /**
     * @param ProductQuoteRecalculateChildrenProfitAmountEvent $event
     */
    public function handle(ProductQuoteRecalculateChildrenProfitAmountEvent $event): void
    {
        try {
            $this->recalculateProfitAmountService->setOffers($event->productQuote->opOffers)->recalculateOffers();
            $this->recalculateProfitAmountService->setOrders($event->productQuote->orpOrders)->recalculateOrders();
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableFormatter($e), 'Listeners:' . self::class);
        }
    }
}