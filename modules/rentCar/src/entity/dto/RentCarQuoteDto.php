<?php

namespace modules\rentCar\src\entity\dto;

use modules\product\src\entities\productType\ProductType;
use modules\product\src\entities\productTypePaymentMethod\ProductTypePaymentMethodQuery;
use modules\rentCar\src\entity\rentCar\RentCar;
use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use modules\rentCar\src\helpers\RentCarDataParser;
use Yii;

/**
 * Class RentCarQuoteDto
 */
class RentCarQuoteDto
{
    /**
     * @param array $data
     * @param int $productQuoteId
     * @param RentCar $rentCar
     * @return RentCarQuote
     */
    public static function create(array $data, int $productQuoteId, RentCar $rentCar): RentCarQuote
    {
        $model = new RentCarQuote();
        $model->rcq_advantages = RentCarDataParser::getActionable($data);
        $model->rcq_category = RentCarDataParser::getModelCategory($data);
        $model->rcq_currency = RentCarDataParser::getPriceCurrencyCode($data);
        $model->rcq_doors = RentCarDataParser::getAttributeValue($data, 'doors');
        $model->rcq_offer_token = RentCarDataParser::getOfferToken($data, $rentCar->prc_request_hash_key);
        $model->rcq_hash_key = md5($model->rcq_offer_token);
        $model->rcq_image_url = RentCarDataParser::getModelImg($data);
        $model->rcq_json_response = $data;
        $model->rcq_model_name = RentCarDataParser::getModelName($data);
        $model->rcq_options = RentCarDataParser::getOptions($data);
        $model->rcq_product_quote_id = $productQuoteId;
        $model->rcq_rent_car_id = $rentCar->prc_id;
        $model->rcq_seats = RentCarDataParser::getAttributeValue($data, 'person');
        $model->rcq_transmission = RentCarDataParser::getAttributeValue($data, 'transmission');
        $model->rcq_vendor_logo_url = RentCarDataParser::getVendorLogo($data);
        $model->rcq_vendor_name = RentCarDataParser::getVendorName($data);
        $model->rcq_request_hash_key = $rentCar->prc_request_hash_key;
        $model->rcq_car_reference_id = RentCarDataParser::getCarReferenceId($data);

        $days = RentCarDataParser::getNumRentalDays($data);
        $totalPrice = RentCarDataParser::getTotalPrice($data);
        $pricePerDay = $totalPrice / $days;

        $model->rcq_price_per_day = round($pricePerDay, 2);
        $model->rcq_days = $days;
        $model->rcq_system_mark_up = 0; // not data
        $model->rcq_agent_mark_up = 0; // not data

        $paymentFee = ProductTypePaymentMethodQuery::getDefaultPercentFeeByProductType(ProductType::PRODUCT_RENT_CAR);
        if ($paymentFee) {
            $model->rcq_service_fee_percent = $paymentFee;
        } else {
            $productTypeServiceFee = 0;
            $productType = ProductType::find()->select(['pt_service_fee_percent'])->byRentCar()->asArray()->one();
            if ($productType && $productType['pt_service_fee_percent']) {
                $productTypeServiceFee = $productType['pt_service_fee_percent'];
            }
            $model->rcq_service_fee_percent = $productTypeServiceFee;
        }

        $model->rcq_created_dt = date('Y-m-d H:i:s');

        return $model;
    }
}
