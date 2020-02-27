<?php

namespace modules\product\src\entities\productQuote\serializer;

use modules\flight\models\FlightQuote;
use modules\hotel\models\HotelQuote;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\entities\serializer\Serializer;

/**
 * Class ProductQuoteExtraData
 *
 * @property ProductQuote $model
 */
class ProductQuoteSerializer extends Serializer
{
    public function __construct(ProductQuote $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
//            'pq_id',
            'pq_gid',
            'pq_name',
//            'pq_product_id',
            'pq_order_id',
            'pq_description',
            'pq_status_id',
            'pq_price',
            'pq_origin_price',
            'pq_client_price',
            'pq_service_fee_sum',
            'pq_origin_currency',
            'pq_client_currency',
//            'pq_origin_currency_rate',
//            'pq_client_currency_rate',
//            'pq_owner_user_id',
//            'pq_created_user_id',
//            'pq_updated_user_id',
//            'pq_created_dt',
//            'pq_updated_dt',
        ];
    }

    public function getData(): array
    {
        $quoteData = $this->toArray();

        if (!$quote = $this->model->getChildQuote()) {
            $quoteData['data'] = $quote->serialize();
        }

        return $quoteData;
    }
}
