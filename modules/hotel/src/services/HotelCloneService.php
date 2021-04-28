<?php

namespace modules\hotel\src\services;

use modules\hotel\models\Hotel;
use modules\hotel\models\HotelRoom;
use modules\hotel\models\HotelRoomPax;
use modules\hotel\src\entities\hotelRoom\HotelRoomRepository;
use modules\hotel\src\entities\hotelRoomPax\HotelRoomPaxRepository;
use modules\hotel\src\repositories\hotel\HotelRepository;

/**
 * Class HotelCloneService
 *
 * @property HotelRepository $hotelRepository
 * @property HotelRoomRepository $hotelRoomRepository
 * @property HotelRoomPaxRepository $hotelRoomPaxRepository
 */
class HotelCloneService
{
    private HotelRepository $hotelRepository;
    private HotelRoomRepository $hotelRoomRepository;
    private HotelRoomPaxRepository $hotelRoomPaxRepository;

    public function __construct(HotelRepository $hotelRepository, HotelRoomRepository $hotelRoomRepository, HotelRoomPaxRepository $hotelRoomPaxRepository)
    {
        $this->hotelRepository = $hotelRepository;
        $this->hotelRoomRepository = $hotelRoomRepository;
        $this->hotelRoomPaxRepository = $hotelRoomPaxRepository;
    }

    public function clone(int $fromProductId, int $toProductId): void
    {
        $hotel = $this->hotelRepository->findByProduct($fromProductId);

        $cloneHotel = Hotel::clone($hotel, $toProductId);
        $this->hotelRepository->save($cloneHotel);

        foreach ($hotel->hotelRooms as $room) {
            $cloneRoom = HotelRoom::clone($room, $cloneHotel->ph_id);
            $this->hotelRoomRepository->save($cloneRoom);

            foreach ($room->hotelRoomPaxes as $pax) {
                $clonePax = HotelRoomPax::clone($pax, $cloneRoom->hr_id);
                $this->hotelRoomPaxRepository->save($clonePax);
            }
        }
    }
}
