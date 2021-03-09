<?php

namespace modules\flight\src\services\flightQuote;

use modules\flight\models\FlightQuote;
use modules\flight\src\helpers\FlightQuoteHelper;
use sales\services\CurrencyHelper;

class FlightQuotePriceCalculator
{
    public function calculate(FlightQuote $quote, $originCurrencyRate): array
    {
        $priceData = FlightQuoteHelper::getPricesData($quote);

        $originPrice = CurrencyHelper::convertToBaseCurrency($priceData->total->net, $originCurrencyRate);
        $appMarkup = CurrencyHelper::convertToBaseCurrency($priceData->total->markUp, $originCurrencyRate);
        // agent_markup - already in base currency
        $agentMarkup = $priceData->total->extraMarkUp;

        return [
            'originPrice' => $originPrice,
            'appMarkup' => $appMarkup,
            'agentMarkup' => $agentMarkup,
        ];
    }
}
