<?php

namespace modules\hotel\src\repositories\hotel;

use modules\hotel\models\Hotel;
use modules\hotel\src\exceptions\HotelCodeException;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

/**
 * Class HotelRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class HotelRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $id): Hotel
    {
        if ($hotel = Hotel::findOne($id)) {
            return $hotel;
        }
        throw new NotFoundException('Hotel is not found', HotelCodeException::HOTEL_NOT_FOUND);
    }

    public function findByProduct(int $id): Hotel
    {
        if ($hotel = Hotel::find()->byProduct($id)->one()) {
            return $hotel;
        }
        throw new NotFoundException('Hotel is not found', HotelCodeException::HOTEL_NOT_FOUND);
    }

    public function save(Hotel $hotel): int
    {
        if (!$hotel->save(false)) {
            throw new \RuntimeException('Saving error', HotelCodeException::HOTEL_SAVE);
        }
        $this->eventDispatcher->dispatchAll($hotel->releaseEvents());
        return $hotel->ph_id;
    }

    public function remove(Hotel $hotel): void
    {
        if (!$hotel->delete()) {
            throw new \RuntimeException('Removing error', HotelCodeException::HOTEL_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($hotel->releaseEvents());
    }
}
