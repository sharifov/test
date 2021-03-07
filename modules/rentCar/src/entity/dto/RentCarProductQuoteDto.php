<?php

namespace modules\rentCar\src\entity\dto;

use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productType\ProductType;
use modules\rentCar\src\entity\rentCar\RentCar;
use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use modules\rentCar\src\helpers\RentCarDataParser;
use modules\rentCar\src\helpers\RentCarQuoteHelper;
use sales\helpers\product\ProductQuoteHelper;
use Yii;

/**
 * Class RentCarProductQuoteDto
 */
class RentCarProductQuoteDto
{
    /**
     * @param RentCar $rentCar
     * @param array $data
     * @param int|null $ownerId
     * @return ProductQuote
     */
    public static function create(RentCar $rentCar, array $data, ?int $ownerId): ProductQuote
    {
        $totalPrice = $rentCar->calculateDays() * RentCarDataParser::getPricePerDay($data);

        $productQuoteDto = new RentCarProductQuoteCreateDto();
        $productQuoteDto->productId = $rentCar->prc_product_id;

        $productQuoteDto->ownerUserId = $ownerId;
        $productQuoteDto->name = RentCarQuoteHelper::nameGenerator($rentCar, $data);

        $productQuoteDto->originPrice = $totalPrice;
        $productQuoteDto->originCurrency = RentCarDataParser::getPriceCurrencyCode($data);
        $productQuoteDto->originCurrencyRate = 1;

        $productQuoteDto->clientCurrencyRate = ProductQuoteHelper::getClientCurrencyRate($rentCar->prcProduct);
        $productQuoteDto->clientCurrency = ProductQuoteHelper::getClientCurrencyCode($rentCar->prcProduct);

        $productTypeServiceFee = null;
        $productType = ProductType::find()->select(['pt_service_fee_percent'])->byRentCar()->asArray()->one();
        if ($productType && $productType['pt_service_fee_percent']) {
            $productTypeServiceFee = $productType['pt_service_fee_percent'];
        }

        return ProductQuote::create($productQuoteDto, $productTypeServiceFee);
    }

    /**
     * @param ProductQuote $productQuote
     * @param RentCarQuote $rentCarQuote
     * @return ProductQuote
     */
    public static function priceUpdate(ProductQuote $productQuote, RentCarQuote $rentCarQuote)
    {
        $productQuote->pq_service_fee_sum = (
            $productQuote->pq_origin_price +
            $rentCarQuote->getSystemMarkUp() +
            $rentCarQuote->getAgentMarkUp()
        ) * ($rentCarQuote->rcq_service_fee_percent / 100);

        $productQuote->pq_price = $productQuote->pq_origin_price +
            $rentCarQuote->getSystemMarkUp() +
            $rentCarQuote->getAgentMarkUp() +
            $productQuote->pq_service_fee_sum;

        $productQuote->pq_client_price = $productQuote->pq_price * $productQuote->pq_client_currency_rate;

        return $productQuote;
    }
}
