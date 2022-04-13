<?php

namespace src\services\quote\quotePriceService;

use common\models\Currency;
use common\models\Quote;
use common\models\QuotePrice;
use src\services\CurrencyHelper;
use yii\helpers\ArrayHelper;

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

    public function setClientCurrency(?string $currencyCode): ClientQuotePriceService
    {
        $this->quote->q_client_currency = $currencyCode;
        return $this;
    }

    public function calculateClientCurrencyRate(): ClientQuotePriceService
    {
        if ($currencyCode = $this->quote->q_client_currency) {
            $this->quote->q_client_currency_rate = Currency::getBaseRateByCurrencyCode($currencyCode);
        }
        return $this;
    }

    public function setClientCurrencyRate(?float $rate): ClientQuotePriceService
    {
        $this->quote->q_client_currency_rate = $rate;
        return $this;
    }

    public function getClientQuotePricePassengersData(): array
    {
        $priceData = $this->getClientPricesData();
        $result = [
            'prices' => [
                'totalPrice' => round($priceData['total']['selling'], 2),
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
            $result['passengers'][$paxCode]['price'] = round($price['selling'] / $price['tickets'], 2);
            $result['passengers'][$paxCode]['tax'] = round(($price['taxes'] + $price['mark_up'] + $price['extra_mark_up'] + $price['service_fee']) / $price['tickets'], 2);
            $result['passengers'][$paxCode]['baseFare'] = round($price['fare'] / $price['tickets'], 2);
            $result['passengers'][$paxCode]['mark_up'] = round($price['mark_up'] / $price['tickets'], 2);
            $result['passengers'][$paxCode]['extra_mark_up'] = round($price['extra_mark_up'] / $price['tickets'], 2);
            $result['passengers'][$paxCode]['baseTax'] = round(($price['taxes']) / $price['tickets'], 2);
            $result['passengers'][$paxCode]['service_fee'] = round($price['service_fee'] ?? 0, 2);

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
            'fare'          => 0,
            'taxes'         => 0,
            'net'           => 0,
            'tickets'              => 0,
            'mark_up'       => 0,
            'extra_mark_up' => 0,
            'service_fee'   => 0,
            'selling'       => 0,
        ];
        $total = $defData;

        $paxCode = null;
        foreach ($this->quote->quotePrices as $price) {
            if ($paxCode !== $price->passenger_type) {
                $prices[$price->passenger_type] = $defData;
                $paxCode = $price->passenger_type;
            }
            $prices[$price->passenger_type]['fare']          += $price->qp_client_fare;
            $prices[$price->passenger_type]['taxes']         += $price->qp_client_taxes;
            $prices[$price->passenger_type]['net']           = $prices[$price->passenger_type]['fare']
                                                                      + $prices[$price->passenger_type]['taxes'];
            $prices[$price->passenger_type]['tickets']              += 1;
            $prices[$price->passenger_type]['mark_up']       += $price->qp_client_markup;
            $prices[$price->passenger_type]['extra_mark_up'] += $price->qp_client_extra_mark_up;
            $prices[$price->passenger_type]['selling']       =  $prices[$price->passenger_type]['net']
                                                                       + $prices[$price->passenger_type]['mark_up']
                                                                       + $prices[$price->passenger_type]['extra_mark_up'];
            if ($service_fee_percent > 0) {
                $prices[$price->passenger_type]['service_fee'] = QuotePrice::calculateProcessingFeeAmount((float)$prices[$price->passenger_type]['selling'], (float)$service_fee_percent);
                $prices[$price->passenger_type]['selling'] += $prices[$price->passenger_type]['service_fee'];
            }
            $prices[$price->passenger_type]['selling'] = round($prices[$price->passenger_type]['selling'], 2);
        }

        foreach ($prices as $key => $price) {
            $total['tickets'] += $price['tickets'];
            $total['net'] += $price['net'];
            $total['mark_up'] += $price['mark_up'];
            $total['extra_mark_up'] += $price['extra_mark_up'];
            $total['selling'] += $price['selling'];

            $prices[$key]['selling'] = round($price['selling'], 2);
            $prices[$key]['net'] = round($price['net'], 2);
        }

        return [
            'prices'              => $prices,
            'total'               => $total,
            'service_fee_percent' => $service_fee_percent,
            'service_fee'         => ($service_fee_percent > 0) ? $total['selling'] * $service_fee_percent / 100 : 0,
            'processing_fee'      => $this->quote->getProcessingFee(),
        ];
    }

    public function geClientPricePerPax()
    {
        $priceData = (new ClientQuotePriceService($this->quote))->getClientPricesData();
        $unknownType = null;
        if (isset($priceData['prices'])) {
            foreach ($priceData['prices'] as $paxCode => $priceEntry) {
                if ($paxCode == QuotePrice::PASSENGER_ADULT) {
                    return round($priceEntry['selling'] / $priceEntry['tickets'], 2);
                }
                if (!ArrayHelper::keyExists($paxCode, QuotePrice::PASSENGER_TYPE_LIST)) {
                    $unknownType = $paxCode;
                }
            }
        }
        if (!empty($priceData['prices']) && $unknownType) {
            $selling = ArrayHelper::getValue($priceData, 'prices.' . $unknownType . '.selling', 0);
            $tickets = ArrayHelper::getValue($priceData, 'prices.' . $unknownType . '.tickets', 1);
            return round($selling / $tickets, 2);
        }

        return 0;
    }

    public function getQuote(): Quote
    {
        return $this->quote;
    }
}
