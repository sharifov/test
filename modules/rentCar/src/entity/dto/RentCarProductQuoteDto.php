<?php

namespace modules\rentCar\src\entity\dto;

use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
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
     * @return ProductQuote
     */
    public static function create(RentCar $rentCar, array $data): ProductQuote
    {
        $totalPrice = $rentCar->calculateDays() * RentCarDataParser::getPricePerDay($data);

        $model = new ProductQuote();
        $model->pq_product_id = $rentCar->prc_product_id;
        $model->pq_origin_currency = RentCarDataParser::getPriceCurrencyCode($data);
        $model->pq_client_currency = ProductQuoteHelper::getClientCurrencyCode($rentCar->prcProduct);

        $model->pq_owner_user_id = Yii::$app->user->id;
        $model->pq_price = $totalPrice;
        $model->pq_origin_price = $totalPrice;
        $model->pq_client_price = $totalPrice;
        $model->pq_status_id = ProductQuoteStatus::PENDING;
        $model->pq_gid = ProductQuote::generateGid();
        $model->pq_service_fee_sum = 0;
        $model->pq_client_currency_rate = ProductQuoteHelper::getClientCurrencyRate($rentCar->prcProduct);
        $model->pq_origin_currency_rate = 1;
        $model->pq_name = RentCarQuoteHelper::nameGenerator($rentCar, $data);

        return $model;
    }
}
