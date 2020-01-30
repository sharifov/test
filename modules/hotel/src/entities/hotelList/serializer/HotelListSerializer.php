<?php

namespace modules\hotel\src\entities\hotelList\serializer;

use modules\hotel\models\HotelList;
use sales\entities\serializer\Serializer;

/**
 * Class HotelListSerializer
 *
 * @property HotelList $model
 */
class HotelListSerializer extends Serializer
{
    public function __construct(HotelList $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            //'hl_id',
            //'hl_code',
            //'hl_hash_key',
            'hl_name',
            'hl_star',
            'hl_category_name',
            //'hl_destination_code',
            'hl_destination_name',
            'hl_zone_name',
            //'hl_zone_code',
            'hl_country_code',
            'hl_state_code',
            'hl_description',
            'hl_address',
            'hl_postal_code',
            'hl_city',
            'hl_email',
            'hl_web',
            'hl_phone_list',
            'hl_image_list',
            'hl_image_base_url',
            //'hl_board_codes',
            //'hl_segment_codes',
            //'hl_latitude',
            //'hl_longitude',
            //'hl_ranking',
        ];
    }

    public function getData(): array
    {
        $data = $this->toArray();

        $imgList = isset($data['hl_image_list']) ? @json_decode($data['hl_image_list'], true) : [];
        $data['hl_image_list'] = [];

        if ($imgList) {
            $i = 0;
            foreach ($imgList as $item) {
                $data['hl_image_list'][] = $item;
                if (++$i > 2) {
                    break;
                }
            }
        }

        //$data['hl_image_list'] = isset($data['hl_image_list']) ? @json_decode($data['hl_image_list'], true) : [];
        $data['hl_phone_list'] = isset($data['hl_phone_list']) ? @json_decode($data['hl_phone_list'], true) : [];

        //$data['hl_image_base_url'] = 'https://dev-hotels.travel-dev.com/hotel/img/';

        return $data;
    }
}
