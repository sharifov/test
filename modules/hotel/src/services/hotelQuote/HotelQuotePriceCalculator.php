<?php

namespace modules\hotel\src\services\hotelQuote;

use modules\hotel\models\HotelQuote;
use modules\hotel\src\helpers\HotelQuoteHelper;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\services\CurrencyHelper;

class HotelQuotePriceCalculator
{
    public function calculate(ProductQuote $productQuote, HotelQuote $hotelQuote): void
    {
        $priceData = HotelQuoteHelper::getPricesData($hotelQuote);

        $productQuote->pq_origin_price = CurrencyHelper::convertToBaseCurrency(($priceData->total->net * $hotelQuote->getCountDays()), $productQuote->pq_origin_currency_rate);
        $productQuote->pq_app_markup = CurrencyHelper::convertToBaseCurrency($priceData->total->systemMarkup * $hotelQuote->getCountDays(), $productQuote->pq_origin_currency_rate);
        // pq_agent_markup - already in base currency
        $productQuote->pq_agent_markup = $priceData->total->agentMarkup * $hotelQuote->getCountDays();

        $productQuote->calculateServiceFeeSum();
        $productQuote->calculatePrice();
        $productQuote->calculateClientPrice();
    }
}
