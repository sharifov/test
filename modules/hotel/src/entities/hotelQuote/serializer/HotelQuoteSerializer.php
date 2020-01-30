<?php

namespace modules\hotel\src\entities\hotelQuote\serializer;

use modules\hotel\models\HotelQuote;
use sales\entities\serializer\Serializer;

/**
 * Class HotelQuoteExtraData
 *
 * @property HotelQuote $model
 */
class HotelQuoteSerializer extends Serializer
{
    public function __construct(HotelQuote $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [];
    }

    public function getData(): array
    {
        $data = [];
        $hotelQuoteRoomData = [];
        if ($this->model->hotelQuoteRooms) {
            foreach ($this->model->hotelQuoteRooms as $hotelQuoteRoom) {
                $hotelQuoteRoomData[] = $hotelQuoteRoom->serialize();
            }
        }

        $data['hotel'] = $this->model->hqHotelList ? $this->model->hqHotelList->serialize() : [];
        $data['rooms'] = $hotelQuoteRoomData;
        return $data;
    }
}
