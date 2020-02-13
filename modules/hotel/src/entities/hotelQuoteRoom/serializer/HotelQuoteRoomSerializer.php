<?php

namespace modules\hotel\src\entities\hotelQuoteRoom\serializer;

use modules\hotel\models\HotelQuoteRoom;
use sales\entities\serializer\Serializer;

/**
 * Class HotelQuoteRoomExtraData
 * @property HotelQuoteRoom $model
 */
class HotelQuoteRoomSerializer extends Serializer
{
    public function __construct(HotelQuoteRoom $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            //'hqr_id',
            'hqr_room_name',
            //'hqr_key',
            //'hqr_code',
            'hqr_class',
            'hqr_amount',
            'hqr_currency',
            'hqr_cancel_amount',
            'hqr_cancel_from_dt',
            // 'hqr_payment_type',
            //'hqr_board_code',
            'hqr_board_name',
            'hqr_rooms',
            'hqr_adults',
            'hqr_children',
        ];
    }

    public function getData(): array
    {
        return $this->toArray();
    }
}
