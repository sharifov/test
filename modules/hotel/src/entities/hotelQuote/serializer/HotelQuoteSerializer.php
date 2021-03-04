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
        return [
            'hq_hash_key',
            'hq_destination_name',
            'hq_hotel_name',
            'hq_request_hash',
            'hq_booking_id',
            'hq_json_booking',
        ];
    }

    public function getData(): array
    {
        $data = $this->toArray();

        $data['hotel_request'] = $this->model->hqHotel ? $this->model->hqHotel->serialize() : [];

        $data['hotel'] = $this->model->hqHotelList ? $this->model->hqHotelList->serialize() : [];
        $data['hotel']['json_booking'] = $this->model->hq_json_booking;

        if ($this->model->hotelQuoteRooms) {
            $data['rooms'] = [];
            foreach ($this->model->hotelQuoteRooms as $hotelQuoteRoom) {
                $data['rooms'][] = $hotelQuoteRoom->serialize();
            }
        }

        return $data;
    }
}
