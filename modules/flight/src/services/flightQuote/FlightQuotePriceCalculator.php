<?php

namespace modules\flight\src\services\flightQuote;

use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuotePaxPrice;
use modules\flight\src\helpers\FlightQuoteHelper;
use src\services\CurrencyHelper;

class FlightQuotePriceCalculator
{
    public function calculate(FlightQuote $quote, $originCurrencyRate): array
    {
        $priceData = FlightQuoteHelper::getPricesData($quote);

        $originPrice = $priceData->total->net;
        $appMarkup = $priceData->total->markUp;
        // agent_markup - already in base currency
        $agentMarkup = $priceData->total->extraMarkUp;

        return [
            'originPrice' => $originPrice,
            'appMarkup' => $appMarkup,
            'agentMarkup' => $agentMarkup,
        ];
    }

    public static function pricesDataC2b(FlightQuote $flightQuote)
    {
        $paxPricesSummary = FlightQuotePaxPrice::find()
            ->select([
                'minus_percent_profit' => 'SUM(ps.ps_percent)',
            ])
            ->andWhere(['qpp_flight_quote_id' => $flightQuote->fq_id])
            ->orderBy(['qpp_flight_pax_code_id' => SORT_ASC])
            ->all();
    }
}
