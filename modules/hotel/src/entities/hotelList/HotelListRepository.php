<?php

namespace modules\hotel\src\entities\hotelList;

use modules\hotel\models\HotelList;
use modules\hotel\src\exceptions\HotelCodeException;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

/**
 * Class HotelListRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class HotelListRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $id): HotelList
    {
        if ($list = HotelList::findOne($id)) {
            return $list;
        }
        throw new NotFoundException('Hotel List is not found', HotelCodeException::HOTEL_LIST_NOT_FOUND);
    }

    public function save(HotelList $list): int
    {
        if (!$list->save(false)) {
            throw new \RuntimeException('Saving error', HotelCodeException::HOTEL_LIST_SAVE);
        }
        $this->eventDispatcher->dispatchAll($list->releaseEvents());
        return $list->hl_id;
    }

    public function remove(HotelList $list): void
    {
        if (!$list->delete()) {
            throw new \RuntimeException('Removing error', HotelCodeException::HOTEL_LIST_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($list->releaseEvents());
    }
}
