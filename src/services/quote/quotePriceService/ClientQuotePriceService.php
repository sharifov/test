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
        $quotePricePassengersData = $this->quote->getQuotePricePassengersData();
        if ($this->quote->q_client_currency === Currency::DEFAULT_CURRENCY) {
            $result = $quotePricePassengersData;
        } else {
            $result = [
                'prices' => [
                    'totalPrice' => CurrencyHelper::convertFromBaseCurrency($quotePricePassengersData['prices']['totalPrice'], $this->quote->q_client_currency_rate),
                    'totalTax' => 0,
                    'isCk' => (bool) $this->quote->check_payment,
                ],
                'passengers' => [],
                'currency' => $this->quote->q_client_currency,
                'currencyRate' => $this->quote->q_client_currency_rate,
                'fareType' => $quotePricePassengersData['fareType'],
            ];

            foreach ($quotePricePassengersData['passengers'] as $paxCode => $price) {
                $result['passengers'][$paxCode]['cnt'] = $price['cnt'];
                $result['passengers'][$paxCode]['price'] = CurrencyHelper::convertFromBaseCurrency($price['price'], $this->quote->q_client_currency_rate);
                $result['passengers'][$paxCode]['tax'] = CurrencyHelper::convertFromBaseCurrency($price['tax'], $this->quote->q_client_currency_rate);
                $result['passengers'][$paxCode]['baseFare'] = CurrencyHelper::convertFromBaseCurrency($price['baseFare'], $this->quote->q_client_currency_rate);
                $result['passengers'][$paxCode]['mark_up'] = CurrencyHelper::convertFromBaseCurrency($price['mark_up'], $this->quote->q_client_currency_rate);
                $result['passengers'][$paxCode]['extra_mark_up'] = CurrencyHelper::convertFromBaseCurrency($price['extra_mark_up'], $this->quote->q_client_currency_rate);
            }
            $result['prices']['totalTax'] = CurrencyHelper::convertFromBaseCurrency($quotePricePassengersData['prices']['totalTax'], $this->quote->q_client_currency_rate);
        }

        return $result;
    }
}
