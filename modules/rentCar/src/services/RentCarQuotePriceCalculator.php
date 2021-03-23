<?php

namespace modules\rentCar\src\services;

use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use sales\services\CurrencyHelper;

class RentCarQuotePriceCalculator
{
    public function calculate(RentCarQuote $quote, $originCurrencyRate): array
    {
        $originPrice = CurrencyHelper::convertToBaseCurrency(($quote->rcq_price_per_day * $quote->rcq_days), $originCurrencyRate);
        $appMarkup = CurrencyHelper::convertToBaseCurrency(($quote->rcq_system_mark_up * $quote->rcq_days), $originCurrencyRate);
        $agentMarkup = $quote->rcq_agent_mark_up * $quote->rcq_days;

        return [
            'originPrice' => $originPrice,
            'appMarkup' => $appMarkup,
            'agentMarkup' => $agentMarkup,
        ];
    }
}
