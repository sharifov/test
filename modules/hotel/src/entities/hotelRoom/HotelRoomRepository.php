<?php

namespace modules\hotel\src\entities\hotelRoom;

use modules\hotel\models\HotelRoom;
use modules\hotel\src\exceptions\HotelCodeException;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

/**
 * Class HotelRoomRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class HotelRoomRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $id): HotelRoom
    {
        if ($room = HotelRoom::findOne($id)) {
            return $room;
        }
        throw new NotFoundException('Hotel Room is not found', HotelCodeException::HOTEL_ROOM_NOT_FOUND);
    }

    public function save(HotelRoom $room): int
    {
        if (!$room->save(false)) {
            throw new \RuntimeException('Saving error', HotelCodeException::HOTEL_ROOM_SAVE);
        }
        $this->eventDispatcher->dispatchAll($room->releaseEvents());
        return $room->hr_id;
    }

    public function remove(HotelRoom $room): void
    {
        if (!$room->delete()) {
            throw new \RuntimeException('Removing error', HotelCodeException::HOTEL_ROOM_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($room->releaseEvents());
    }
}
