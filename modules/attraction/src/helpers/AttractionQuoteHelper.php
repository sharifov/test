<?php

namespace modules\attraction\src\helpers;

use sales\helpers\product\ProductQuoteHelper;
use modules\attraction\models\AttractionQuote;

class AttractionQuoteHelper
{
    /**
     * @param  AttractionQuote $attractionQuote
     * @return AttractionQuotePriceDataDTO
     */
    public static function getPricesData(AttractionQuote $attractionQuote): AttractionQuotePriceDataDTO
    {
        /** @var $prices AttractionQuotePaxPriceDataDTO[] */
        $prices = [];
        $service_fee_percent = $attractionQuote->getServiceFeePercent();

        $dtoPax = new AttractionQuotePaxPriceDataDTO();
        $paxCodeId = null;
        $dtoTotal = new AttractionQuoteTotalPriceDTO();
        foreach ($attractionQuote->attractionQuotePricingCategories as $price) {
            $paxCode = $price['atqpc_category_id'];
            if ($dtoPax->paxCodeId !== $paxCode) {
                $dtoPax = new AttractionQuotePaxPriceDataDTO();
                $dtoPax->paxCodeId = $paxCode;
                $dtoPax->paxCode = $paxCode;
                $dtoPax->label = $price['atqpc_label'];
            }

            //$dtoPax->fare += $price->qpp_fare;
            $dtoPax->fare += $price['atqpc_price'];
            //$dtoPax->taxes += $price->qpp_tax;
            $dtoPax->taxes += 0;
            //$dtoPax->net = ($dtoPax->fare + $dtoPax->taxes) * $price->qpp_cnt;
            $dtoPax->net = $dtoPax->fare * $price['atqpc_quantity'];
            //$dtoPax->tickets += $price->qpp_cnt;
            $dtoPax->tickets += $price['atqpc_quantity'];
            //$dtoPax->markUp += $price->qpp_system_mark_up * $price->qpp_cnt;
            $dtoPax->markUp += $price['atqpc_system_mark_up'] * $price['atqpc_quantity'];
            //$dtoPax->extraMarkUp += $price->qpp_agent_mark_up * $price->qpp_cnt;
            $dtoPax->extraMarkUp += $price['atqpc_agent_mark_up'] * $price['atqpc_quantity'];
            //$dtoPax->selling = $dtoPax->net + $dtoPax->markUp + $dtoPax->extraMarkUp;
            $dtoPax->selling = $dtoPax->net + $dtoPax->markUp  + $dtoPax->extraMarkUp;
            //$dtoPax->serviceFee = $dtoPax->selling * $service_fee_percent / 100;
            $dtoPax->serviceFee = $dtoPax->selling * $service_fee_percent / 100;
            //$dtoPax->selling = ProductQuoteHelper::roundPrice($dtoPax->serviceFee + $dtoPax->selling);
            $dtoPax->selling = ProductQuoteHelper::roundPrice($dtoPax->serviceFee + $dtoPax->selling);
            //$dtoPax->clientSelling = ProductQuoteHelper::roundPrice($dtoPax->selling * $attractionQuote->atnqProductQuote->pq_client_currency_rate);
            $dtoPax->clientSelling = ProductQuoteHelper::roundPrice($dtoPax->selling * $attractionQuote->atnqProductQuote->pq_client_currency_rate);

            $prices[$paxCode] = $dtoPax;

            $dtoTotal->tickets += $dtoPax->tickets;
            $dtoTotal->net += $dtoPax->net;
            $dtoTotal->markUp += $dtoPax->markUp;
            $dtoTotal->extraMarkUp += $dtoPax->extraMarkUp;
            $dtoTotal->selling += $dtoPax->selling;
            $dtoTotal->serviceFeeSum += ProductQuoteHelper::roundPrice($dtoPax->serviceFee);
            $dtoTotal->clientSelling += $dtoPax->clientSelling;
        }

        $priceDto = new AttractionQuotePriceDataDTO();
        $priceDto->prices = $prices;
        $priceDto->total = $dtoTotal;
        $priceDto->serviceFeePercent = $service_fee_percent;
        $priceDto->serviceFee = ($priceDto->serviceFeePercent > 0) ? ($dtoTotal->selling * $priceDto->serviceFeePercent / 100) : 0;
        $priceDto->processingFee = $attractionQuote->getProcessingFee();

        return $priceDto;
    }
}
