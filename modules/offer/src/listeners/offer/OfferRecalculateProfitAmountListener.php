<?php

namespace modules\offer\src\listeners\offer;

use modules\offer\src\entities\offer\events\OfferRecalculateProfitAmountEvent;
use sales\helpers\app\AppHelper;
use sales\services\RecalculateProfitAmountService;
use Yii;

/**
 *
 * @property RecalculateProfitAmountService $recalculateProfitAmountService
 */
class OfferRecalculateProfitAmountListener
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
     * @param OfferRecalculateProfitAmountEvent $event
     */
    public function handle(OfferRecalculateProfitAmountEvent $event): void
    {
        try {
            $this->recalculateProfitAmountService->setOffers($event->offers)->recalculateOffers();
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableFormatter($e), 'Listeners:' . self::class);
        }
    }
}