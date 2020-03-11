<?php

namespace modules\hotel\src\entities\hotelQuoteRoom;

use modules\hotel\models\HotelQuoteRoom;
use modules\hotel\src\exceptions\HotelCodeException;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

/**
 * Class HotelQuoteRoomRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class HotelQuoteRoomRepository extends Repository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $id): HotelQuoteRoom
    {
        if ($room = HotelQuoteRoom::findOne($id)) {
            return $room;
        }
        throw new NotFoundException('Hotel Quote Room is not found', HotelCodeException::HOTEL_QUOTE_ROOM_NOT_FOUND);
    }

    public function save(HotelQuoteRoom $room): int
    {
        if (!$room->save(false)) {
            throw new \RuntimeException('Saving error', HotelCodeException::HOTEL_QUOTE_ROOM_SAVE);
        }
        $this->eventDispatcher->dispatchAll($room->releaseEvents());
        return $room->hqr_id;
    }

    public function remove(HotelQuoteRoom $room): void
    {
        if (!$room->delete()) {
            throw new \RuntimeException('Removing error', HotelCodeException::HOTEL_QUOTE_ROOM_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($room->releaseEvents());
    }
}
