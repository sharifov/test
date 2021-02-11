<?php

namespace sales\listeners\lead;

use modules\product\src\entities\productQuoteOption\ProductQuoteOptionRepository;
use modules\product\src\services\ProductQuoteService;
use sales\events\lead\LeadPreferencesUpdateCurrencyEvent;

/**
 * Class LeadPreferencesUpdateCurrencyEventListener
 * @package sales\listeners\lead
 *
 * @property ProductQuoteService $productQuoteService
 */
class LeadPreferencesUpdateCurrencyEventListener
{
    /**
     * @var ProductQuoteService
     */
    private $productQuoteService;

    /**
     * LeadPreferencesUpdateCurrencyEventListener constructor.
     * @param ProductQuoteService $productQuoteService
     */
    public function __construct(ProductQuoteService $productQuoteService)
    {
        $this->productQuoteService = $productQuoteService;
    }

    /**
     * @param LeadPreferencesUpdateCurrencyEvent $event
     */
    public function handle(LeadPreferencesUpdateCurrencyEvent $event): void
    {
        $products = $event->leadPreference->lead->products;
        $clientCurrency = $event->leadPreference->prefCurrency;

        $productQuoteOptionRepository = \Yii::createObject(ProductQuoteOptionRepository::class);

        if ($products) {
            foreach ($products as $product) {
                if ($productQuotes = $product->productQuotes) {
                    foreach ($productQuotes as $productQuote) {
                        try {
                            $this->productQuoteService->recountProductQuoteClientPrice($productQuote, $clientCurrency);
                            $productQuoteOptions = $productQuote->productQuoteOptions;
                            if ($productQuoteOptions) {
                                foreach ($productQuoteOptions as $productQuoteOption) {
                                    $productQuoteOption->calculateClientPrice();
                                    $productQuoteOptionRepository->save($productQuoteOption);
                                }
                            }
                        } catch (\Throwable $e) {
                            \Yii::warning($e->getMessage(), 'LeadPreferencesUpdateCurrencyEventListener::handle::Throwable');
                        }
                    }
                }
            }
        }
    }
}
