<?php

namespace modules\hotel\src\entities\hotelRoomPax;

use modules\hotel\models\HotelRoomPax;
use modules\hotel\src\exceptions\HotelCodeException;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

/**
 * Class HotelRoomPaxRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class HotelRoomPaxRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $id): HotelRoomPax
    {
        if ($pax = HotelRoomPax::findOne($id)) {
            return $pax;
        }
        throw new NotFoundException('Hotel Room Pax is not found', HotelCodeException::HOTEL_ROOM_PAX_NOT_FOUND);
    }

    public function save(HotelRoomPax $pax): int
    {
        if (!$pax->save(false)) {
            throw new \RuntimeException('Saving error', HotelCodeException::HOTEL_ROOM_PAX_SAVE);
        }
        $this->eventDispatcher->dispatchAll($pax->releaseEvents());
        return $pax->hrp_id;
    }

    public function remove(HotelRoomPax $pax): void
    {
        if (!$pax->delete()) {
            throw new \RuntimeException('Removing error', HotelCodeException::HOTEL_ROOM_PAX_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($pax->releaseEvents());
    }
}
