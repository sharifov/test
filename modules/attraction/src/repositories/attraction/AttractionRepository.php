<?php

namespace modules\attraction\src\repositories\attraction;

use modules\attraction\models\Attraction;
use modules\attraction\src\exceptions\AttractionCodeException;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

/**
 * Class FlightRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class AttractionRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(?int $id): Attraction
    {
        if ($attraction = Attraction::findOne($id)) {
            return $attraction;
        }
        throw new NotFoundException('Attraction is not found', AttractionCodeException::ATTRACTION_NOT_FOUND);
    }

    public function save(Attraction $attraction): int
    {
        if (!$attraction->save(false)) {
            throw new \RuntimeException('Saving error', AttractionCodeException::ATTRACTION_SAVE);
        }
        $this->eventDispatcher->dispatchAll($attraction->releaseEvents());
        return $attraction->atn_id;
    }

    public function remove(Attraction $attraction): void
    {
        if (!$attraction->delete()) {
            throw new \RuntimeException('Removing error', AttractionCodeException::ATTRACTION_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($attraction->releaseEvents());
    }
}
