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
        \yii\helpers\VarDumper::dump(['Listener' => $event], 10, true); exit();  /* TODO: to remove */

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->recalculateProfitAmountService->recalculate($event->productQuoteId);
            $this->recalculateProfitAmountService->saveProductQuote();

            if ($this->recalculateProfitAmountService->changedOffers) {
                $this->recalculateProfitAmountService->saveOffers();
            }
            if ($this->recalculateProfitAmountService->changedOrders) {
                $this->recalculateProfitAmountService->saveOrders();
            }
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error(AppHelper::throwableFormatter($e), 'Listeners:' . self::class);
        }
    }
}