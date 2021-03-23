<?php

namespace modules\attraction\src\services\attractionQuote;

use modules\attraction\models\AttractionQuote;
use modules\attraction\src\helpers\AttractionQuoteHelper;
use sales\services\CurrencyHelper;

class AttractionQuotePriceCalculator
{
    public function calculate(AttractionQuote $quote, $originCurrencyRate): array
    {
        $priceData = AttractionQuoteHelper::getPricesData($quote);

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
