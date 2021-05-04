<?php

namespace modules\hotel\src\entities\hotelRoomPax;

use modules\hotel\models\HotelRoomPax;

class HotelRoomPaxQuery
{
    public static function adultsExistByRoomId(int $id): bool
    {
        $query = HotelRoomPax::find();
        $query->where(['hrp_type_id' => HotelRoomPax::PAX_TYPE_ADL]);
        $query->andWhere(['hrp_hotel_room_id' => $id]);
        return $query->exists();
    }
}
