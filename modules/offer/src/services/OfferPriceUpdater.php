<?php

namespace modules\offer\src\services;

use modules\offer\src\entities\offer\OfferRepository;
use modules\offer\src\entities\offerProduct\OfferProduct;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\services\CurrencyHelper;

/**
 * Class OfferPriceUpdater
 *
 * @property OfferRepository $offerRepository
 */
class OfferPriceUpdater
{
    private OfferRepository $offerRepository;

    public function __construct(OfferRepository $offerRepository)
    {
        $this->offerRepository = $offerRepository;
    }

    public function update(int $offerId): void
    {
        $offer = $this->offerRepository->find($offerId);

        $quotes = ProductQuote::find()->andWhere([
            'pq_id' => OfferProduct::find()->select(['op_product_quote_id'])->andWhere(['op_offer_id' => $offer->of_id])
        ])->all();

        $appTotal = 0;
        $profitAmount = 0;
        foreach ($quotes as $quote) {
            if ($quote->pq_price) {
                $appTotal += $quote->pq_price;
            }
            foreach ($quote->productQuoteOptions as $option) {
                if ($option->pqo_price) {
                    $appTotal += $option->pqo_price;
                }
                if ($option->pqo_extra_markup) {
                    $appTotal += $option->pqo_extra_markup;
                    $profitAmount += $option->pqo_extra_markup;
                }
            }
            if ($quote->pq_profit_amount) {
                $profitAmount += $quote->pq_profit_amount;
            }
        }
        $offer->of_app_total = $appTotal;
        $offer->of_profit_amount = $profitAmount;
        if ($offer->of_client_currency_rate) {
            if ($offer->of_app_total) {
                $offer->of_client_total = CurrencyHelper::convertFromBaseCurrency($offer->of_app_total, $offer->of_client_currency_rate);
            }
        } else {
            $offer->of_client_total = $offer->of_app_total;
        }
        $this->offerRepository->save($offer);
    }
}
