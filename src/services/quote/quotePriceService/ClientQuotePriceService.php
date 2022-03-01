<?php

namespace src\services\quote\quotePriceService;

use common\models\Currency;
use common\models\Quote;
use common\models\QuotePrice;
use src\services\CurrencyHelper;

/**
 * Class ClientQuotePriceService
 */
class ClientQuotePriceService
{
    private Quote $quote;

    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }

    public function getClientQuotePricePassengersData(): array
    {
        $priceData = $this->getClientPricesData();
        $result = [
            'prices' => [
                'totalPrice' => round($priceData['total']['client_selling'], 2),
                'totalTax' => 0,
                'isCk' => (bool) $this->quote->check_payment,
            ],
            'passengers' => [],
            'currency' => $this->quote->q_client_currency,
            'currencyRate' => $this->quote->q_client_currency_rate,
            'fareType' => empty($this->quote->fare_type) ? Quote::FARE_TYPE_PUB : $this->quote->fare_type,
        ];

        foreach ($priceData['prices'] as $paxCode => $price) {
            $result['passengers'][$paxCode]['cnt'] = $price['tickets'];
            $result['passengers'][$paxCode]['price'] = round($price['client_selling'] / $price['tickets'], 2);
            $result['passengers'][$paxCode]['tax'] = round(($price['client_taxes'] + $price['client_mark_up'] + $price['client_extra_mark_up'] + $price['client_service_fee']) / $price['tickets'], 2);
            $result['passengers'][$paxCode]['baseFare'] = round($price['client_fare'] / $price['tickets'], 2);
            $result['passengers'][$paxCode]['mark_up'] = round($price['client_mark_up'], 2);
            $result['passengers'][$paxCode]['extra_mark_up'] = round($price['client_extra_mark_up'], 2);

            $result['prices']['totalTax'] += $result['passengers'][$paxCode]['tax'] * $price['tickets'];
        }
        $result['prices']['totalTax'] = round($result['prices']['totalTax'], 2);

        return $result;
    }

    public function getClientPricesData(): array
    {
        $prices = [];
        $service_fee_percent = $this->quote->getServiceFeePercent();
        $defData = [
            'client_fare'          => 0,
            'client_taxes'         => 0,
            'client_net'           => 0,
            'tickets'              => 0,
            'client_mark_up'       => 0,
            'client_extra_mark_up' => 0,
            'client_service_fee'   => 0,
            'client_selling'       => 0,
        ];
        $total = $defData;

        $paxCode = null;
        foreach ($this->quote->quotePrices as $price) {
            if ($paxCode !== $price->passenger_type) {
                $prices[$price->passenger_type] = $defData;
                $paxCode = $price->passenger_type;
            }
            $prices[$price->passenger_type]['client_fare']          += $price->qp_client_fare;
            $prices[$price->passenger_type]['client_taxes']         += $price->qp_client_taxes;
            $prices[$price->passenger_type]['client_net']           = $prices[$price->passenger_type]['client_fare']
                                                                      + $prices[$price->passenger_type]['client_taxes'];
            $prices[$price->passenger_type]['tickets']              += 1;
            $prices[$price->passenger_type]['client_mark_up']       += $price->qp_client_markup;
            $prices[$price->passenger_type]['client_extra_mark_up'] += $price->qp_client_extra_mark_up;
            $prices[$price->passenger_type]['client_selling']       =  $prices[$price->passenger_type]['client_net']
                                                                       + $prices[$price->passenger_type]['client_mark_up']
                                                                       + $prices[$price->passenger_type]['client_extra_mark_up'];
            if ($service_fee_percent > 0) {
                $prices[$price->passenger_type]['client_service_fee'] = QuotePrice::calculateProcessingFeeAmount((float)$prices[$price->passenger_type]['client_selling'], (float)$service_fee_percent);
                $prices[$price->passenger_type]['client_selling'] += $prices[$price->passenger_type]['client_service_fee'];
            }
            $prices[$price->passenger_type]['client_selling'] = round($prices[$price->passenger_type]['client_selling'], 2);
        }

        foreach ($prices as $key => $price) {
            $total['tickets'] += $price['tickets'];
            $total['client_net'] += $price['client_net'];
            $total['client_mark_up'] += $price['client_mark_up'];
            $total['client_extra_mark_up'] += $price['client_extra_mark_up'];
            $total['client_selling'] += $price['client_selling'];

            $prices[$key]['client_selling'] = round($price['client_selling'], 2);
            $prices[$key]['client_net'] = round($price['client_net'], 2);
        }

        return [
            'prices'              => $prices,
            'total'               => $total,
            'service_fee_percent' => $service_fee_percent,
            'service_fee'         => ($service_fee_percent > 0) ? $total['selling'] * $service_fee_percent / 100 : 0,
            'processing_fee'      => $this->quote->getProcessingFee(),
        ];
    }
}
