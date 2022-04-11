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
            $extraMarkUp = $clientExtraMarkUp = (float) $form->clientExtraMarkUp;
            $selling = ($net + $markUp + $extraMarkUp);
            $serviceFee = $clientServiceFee = $quote->service_fee_percent ? self::calculateServiceFee($selling, $form->getQuote()) : 0;
            $selling = $clientSelling = ($selling + $serviceFee);
        } else {
            $clientFare = $form->baseFare;
            $clientTaxes = $form->baseTax;
            $clientMarkUp = $form->markup;
            $clientExtraMarkUp = (float) $form->clientExtraMarkUp;
            $clientNet = ($clientFare + $clientTaxes);
            $clientSelling = ($clientNet + $clientMarkUp + $clientExtraMarkUp);
            $clientServiceFee = $quote->service_fee_percent ? self::calculateServiceFee($clientSelling, $form->getQuote()) : 0;
            $clientSelling += $clientServiceFee;

            $fare = CurrencyHelper::convertToBaseCurrency($clientFare, $quote->q_client_currency_rate);
            $taxes = CurrencyHelper::convertToBaseCurrency($clientTaxes, $quote->q_client_currency_rate);
            $markUp = CurrencyHelper::convertToBaseCurrency($clientMarkUp, $quote->q_client_currency_rate);
            $extraMarkUp = CurrencyHelper::convertToBaseCurrency($clientExtraMarkUp, $quote->q_client_currency_rate);
            $net = CurrencyHelper::convertToBaseCurrency($clientNet, $quote->q_client_currency_rate);
            $serviceFee = CurrencyHelper::convertToBaseCurrency($clientServiceFee, $quote->q_client_currency_rate);
            $selling = CurrencyHelper::convertToBaseCurrency($clientSelling, $quote->q_client_currency_rate);
        }

        $quotePrice->fare = CurrencyHelper::roundUp($fare);
        $quotePrice->taxes = CurrencyHelper::roundUp($taxes);
        $quotePrice->net = CurrencyHelper::roundUp($net);
        $quotePrice->mark_up = CurrencyHelper::roundUp($markUp);
        $quotePrice->extra_mark_up = CurrencyHelper::roundUp($extraMarkUp);
        $quotePrice->selling = CurrencyHelper::roundUp($selling);
        $quotePrice->service_fee = CurrencyHelper::roundUp($serviceFee);

        $quotePrice->qp_client_fare = CurrencyHelper::roundUp($clientFare);
        $quotePrice->qp_client_taxes = CurrencyHelper::roundUp($clientTaxes);
        $quotePrice->qp_client_net = CurrencyHelper::roundUp($clientNet);
        $quotePrice->qp_client_markup = CurrencyHelper::roundUp($clientMarkUp);
        $quotePrice->qp_client_extra_mark_up = CurrencyHelper::roundUp($clientExtraMarkUp);
        $quotePrice->qp_client_selling = CurrencyHelper::roundUp($clientSelling);
        $quotePrice->qp_client_service_fee = CurrencyHelper::roundUp($clientServiceFee);

        return $quotePrice;
    }

    public static function createFromApi(Quote $quote, array $quotePriceAttributes): QuotePrice
    {
        if ($quote->isClientCurrencyDefault()) {
            $fare = $clientFare = $quotePriceAttributes['fare'] ?? null;
            $taxes = $clientTaxes = $quotePriceAttributes['taxes'] ?? null;
            $markUp = $clientMarkUp = $quotePriceAttributes['mark_up'] ?? null;
            $net = $clientNet = $quotePriceAttributes['net'] ?? null;
            $extraMarkUp = $clientExtraMarkUp = $quotePriceAttributes['extra_mark_up'] ?? null;
            $selling = $clientSelling = $quotePriceAttributes['selling'] ?? null;
            $serviceFee = $clientServiceFee = $quotePriceAttributes['service_fee'] ?? null;
        } else {
            $clientFare = $quotePriceAttributes['fare'] ?? null;
            $clientTaxes = $quotePriceAttributes['taxes'] ?? null;
            $clientMarkUp = $quotePriceAttributes['mark_up'] ?? null;
            $clientExtraMarkUp = $quotePriceAttributes['extra_mark_up'] ?? null;
            $clientNet = $quotePriceAttributes['net'] ?? null;
            $clientSelling = $quotePriceAttributes['selling'] ?? null;
            $clientServiceFee = $quotePriceAttributes['service_fee'] ?? null;

            $fare = CurrencyHelper::convertToBaseCurrency($clientFare, $quote->q_client_currency_rate);
            $taxes = CurrencyHelper::convertToBaseCurrency($clientTaxes, $quote->q_client_currency_rate);
            $markUp = CurrencyHelper::convertToBaseCurrency($clientMarkUp, $quote->q_client_currency_rate);
            $extraMarkUp = CurrencyHelper::convertToBaseCurrency($clientExtraMarkUp, $quote->q_client_currency_rate);
            $net = CurrencyHelper::convertToBaseCurrency($clientNet, $quote->q_client_currency_rate);
            $serviceFee = CurrencyHelper::convertToBaseCurrency($clientServiceFee, $quote->q_client_currency_rate);
            $selling = CurrencyHelper::convertToBaseCurrency($clientSelling, $quote->q_client_currency_rate);
        }

        $quotePrice = new QuotePrice();
        $quotePrice->passenger_type = $quotePriceAttributes['passenger_type'] ?? null;
        $quotePrice->quote_id = $quote->id;

        $quotePrice->fare = CurrencyHelper::roundUp($fare);
        $quotePrice->taxes = CurrencyHelper::roundUp($taxes);
        $quotePrice->net = CurrencyHelper::roundUp($net);
        $quotePrice->mark_up = CurrencyHelper::roundUp($markUp);
        $quotePrice->extra_mark_up = CurrencyHelper::roundUp($extraMarkUp);
        $quotePrice->selling = CurrencyHelper::roundUp($selling);
        $quotePrice->service_fee = CurrencyHelper::roundUp($serviceFee);

        $quotePrice->qp_client_fare = CurrencyHelper::roundUp($clientFare);
        $quotePrice->qp_client_taxes = CurrencyHelper::roundUp($clientTaxes);
        $quotePrice->qp_client_net = CurrencyHelper::roundUp($clientNet);
        $quotePrice->qp_client_markup = CurrencyHelper::roundUp($clientMarkUp);
        $quotePrice->qp_client_extra_mark_up = CurrencyHelper::roundUp($clientExtraMarkUp);
        $quotePrice->qp_client_selling = CurrencyHelper::roundUp($clientSelling);
        $quotePrice->qp_client_service_fee = CurrencyHelper::roundUp($clientServiceFee);

        return $quotePrice;
    }

    private static function calculateServiceFee(float $selling, ?Quote $quote): float
    {
        $serviceFeePercent = $quote ? $quote->getServiceFeePercent() : (new Quote())->serviceFeePercent;
        return QuotePrice::calculateProcessingFeeAmount($selling, (float) $serviceFeePercent);
    }
}
