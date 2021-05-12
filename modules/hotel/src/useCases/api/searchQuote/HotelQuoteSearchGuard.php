<?php

namespace modules\hotel\src\useCases\api\searchQuote;

use modules\hotel\models\Hotel;
use modules\hotel\models\HotelRoom;
use modules\hotel\src\entities\hotelRoomPax\HotelRoomPaxQuery;

class HotelQuoteSearchGuard
{
    /**
     * @param Hotel $hotel
     * @return Hotel
     */
    public static function guard(Hotel $hotel): Hotel
    {
        if (!$hotel->ph_check_in_date) {
            throw new \DomainException('Missing check in date in Hotel data; Fill Hotel data;');
        }

        if (!$hotel->ph_check_out_date) {
            throw  new \DomainException('Missing check out date in Hotel data; Fill Hotel data;');
        }

        if (!$hotel->ph_destination_code) {
            throw new \DomainException('Missing destination in Hotel data; Fill Hotel data;');
        }

        if (empty($hotel->hotelRooms)) {
            throw new \DomainException('Missing rooms in Hotel data; Add rooms;');
        }

        self::checkIfAdultsExists($hotel->hotelRooms);

        return $hotel;
    }

    /**
     * @param HotelRoom[] $hotelRooms
     * @return bool
     */
    private static function checkIfAdultsExists(array $hotelRooms): bool
    {
        foreach ($hotelRooms as $hotelRoom) {
            if (!HotelRoomPaxQuery::adultsExistByRoomId($hotelRoom->hr_id)) {
                throw new \DomainException('Not found adult passengers in one of the rooms; Update hotel room data;');
            }
        }
        return true;
    }
}
