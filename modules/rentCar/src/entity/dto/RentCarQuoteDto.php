<?php

namespace modules\rentCar\src\entity\dto;

use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use modules\rentCar\src\helpers\RentCarDataParser;

/**
 * Class RentCarQuoteDto
 */
class RentCarQuoteDto
{
    /**
     * @param array $data
     * @param int $productQuoteId
     * @param int $rentCarId
     * @param string $requestHash
     * @return RentCarQuote
     */
    public static function create(array $data, int $productQuoteId, int $rentCarId, string $requestHash): RentCarQuote
    {
        $model = new RentCarQuote();
        $model->rcq_advantages = RentCarDataParser::getActionable($data);
        $model->rcq_category = RentCarDataParser::getModelCategory($data);
        $model->rcq_currency = RentCarDataParser::getPriceCurrencyCode($data);
        $model->rcq_days = null;
        $model->rcq_doors = RentCarDataParser::getAttributeValue($data, 'doors');
        $model->rcq_offer_token = RentCarDataParser::getOfferToken($data, $requestHash);
        $model->rcq_hash_key = md5($model->rcq_offer_token);
        $model->rcq_image_url = RentCarDataParser::getModelImg($data);
        $model->rcq_json_response = $data;
        $model->rcq_model_name = RentCarDataParser::getModelName($data);
        $model->rcq_options = RentCarDataParser::getOptions($data);
        $model->rcq_price_per_day = RentCarDataParser::getPricePerDay($data);
        $model->rcq_product_quote_id = $productQuoteId;
        $model->rcq_rent_car_id = $rentCarId;
        $model->rcq_seats = RentCarDataParser::getAttributeValue($data, 'person');
        $model->rcq_transmission = RentCarDataParser::getAttributeValue($data, 'transmission');
        $model->rcq_vendor_logo_url = RentCarDataParser::getVendorLogo($data);
        $model->rcq_vendor_name = RentCarDataParser::getVendorName($data);
        $model->rcq_request_hash_key = $requestHash;


        return $model;
    }
}
