<?php

namespace modules\hotel\src\services\hotelQuote;

use modules\hotel\models\HotelQuote;
use modules\hotel\src\helpers\HotelQuoteHelper;
use sales\services\CurrencyHelper;

class HotelQuotePriceCalculator
{
    public function calculate(HotelQuote $hotelQuote, $originCurrencyRate): array
    {
        $priceData = HotelQuoteHelper::getPricesData($hotelQuote);

        $originPrice = CurrencyHelper::convertToBaseCurrency(($priceData->total->net * $hotelQuote->getCountDays()), $originCurrencyRate);
        $appMarkup = CurrencyHelper::convertToBaseCurrency($priceData->total->systemMarkup * $hotelQuote->getCountDays(), $originCurrencyRate);
        // pq_agent_markup - already in base currency
        $agentMarkup = $priceData->total->agentMarkup * $hotelQuote->getCountDays();

        return [
            'originPrice' => $originPrice,
            'appMarkup' => $appMarkup,
            'agentMarkup' => $agentMarkup,
        ];
    }
}
