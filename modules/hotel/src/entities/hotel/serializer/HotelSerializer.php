<?php

namespace modules\hotel\src\entities\hotel\serializer;

use modules\hotel\models\Hotel;
use sales\entities\serializer\Serializer;

/**
 * Class HotelSerializer
 *
 * @property Hotel $model
 */
class HotelSerializer extends Serializer
{
    public function __construct(Hotel $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            'ph_product_id',
            'ph_check_in_date',
            'ph_check_out_date',
            'ph_zone_code',
            'ph_hotel_code',
            'ph_destination_code',
            'ph_destination_label',
            'ph_min_star_rate',
            'ph_max_star_rate',
            'ph_max_price_rate',
            'ph_min_price_rate',
        ];
    }

    public function getData(): array
    {
        return $this->toArray();
    }
}
