<?php

namespace src\services\quote\addQuote\price;

use common\models\Currency;
use common\models\Quote;
use common\models\QuotePrice;
use src\services\CurrencyHelper;

/**
 * Class QuotePriceCreateService
 */
class QuotePriceCreateService
{
    public static function createFromSearch(QuotePriceSearchForm $form): QuotePrice
    {
        $quote = $form->getQuote();
        $quotePrice = new QuotePrice();
        $quotePrice->passenger_type = $form->paxCode;

        if ($quote->q_client_currency === Currency::DEFAULT_CURRENCY) {
            $fare = $clientFare = $form->baseFare;
            $taxes = $clientTaxes = $form->baseTax;
            $markUp = $clientMarkUp = $form->markup;
            $net = $clientNet = ($fare + $taxes);
            $selling = ($net + $markUp);
            $serviceFee = $clientServiceFee = $form->checkPayment ? self::calculateServiceFee($selling) : 0;
            $selling = $clientSelling = ($selling + $serviceFee);
        } else {
            $clientFare = $form->baseFare;
            $clientTaxes = $form->baseTax;
            $clientMarkUp = $form->markup;
            $clientNet = ($clientFare + $clientTaxes);
            $clientSelling = $clientNet + $clientMarkUp;
            $clientServiceFee = $form->checkPayment ? self::calculateServiceFee($clientSelling) : 0;
            $clientSelling += $clientServiceFee;

            $fare = CurrencyHelper::convertToBaseCurrency($clientFare, $quote->q_client_currency_rate);
            $taxes = CurrencyHelper::convertToBaseCurrency($clientTaxes, $quote->q_client_currency_rate);
            $markUp = CurrencyHelper::convertToBaseCurrency($clientMarkUp, $quote->q_client_currency_rate);
            $net = CurrencyHelper::convertToBaseCurrency($clientNet, $quote->q_client_currency_rate);
            $serviceFee = CurrencyHelper::convertToBaseCurrency($clientServiceFee, $quote->q_client_currency_rate);
            $selling = CurrencyHelper::convertToBaseCurrency($clientSelling, $quote->q_client_currency_rate);
        }

        $quotePrice->fare = CurrencyHelper::roundUp($fare);
        $quotePrice->taxes = CurrencyHelper::roundUp($taxes);
        $quotePrice->net = CurrencyHelper::roundUp($net);
        $quotePrice->mark_up = CurrencyHelper::roundUp($markUp);
        $quotePrice->selling = CurrencyHelper::roundUp($selling);
        $quotePrice->service_fee = CurrencyHelper::roundUp($serviceFee);

        $quotePrice->qp_client_fare = CurrencyHelper::roundUp($clientFare);
        $quotePrice->qp_client_taxes = CurrencyHelper::roundUp($clientTaxes);
        $quotePrice->qp_client_net = CurrencyHelper::roundUp($clientNet);
        $quotePrice->qp_client_markup = CurrencyHelper::roundUp($clientMarkUp);
        $quotePrice->qp_client_selling = CurrencyHelper::roundUp($clientSelling);
        $quotePrice->qp_client_service_fee = CurrencyHelper::roundUp($clientServiceFee);

        return $quotePrice;
    }

    private static function calculateServiceFee(float $selling): float
    {
        return QuotePrice::calculateProcessingFeeAmount($selling, (new Quote())->serviceFeePercent);
    }
}
