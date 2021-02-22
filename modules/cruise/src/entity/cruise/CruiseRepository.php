<?php

namespace modules\cruise\src\entity\cruise;

use modules\cruise\src\exceptions\CruiseCodeException;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

class CruiseRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $id): Cruise
    {
        if ($cruise = Cruise::findOne($id)) {
            return $cruise;
        }
        throw new NotFoundException('Cruise is not found', CruiseCodeException::CRUISE_NOT_FOUND);
    }

    public function save(Cruise $cruise): int
    {
        if (!$cruise->save(false)) {
            throw new \RuntimeException('Saving error', CruiseCodeException::CRUISE_SAVE);
        }
        $this->eventDispatcher->dispatchAll($cruise->releaseEvents());
        return $cruise->crs_id;
    }

    public function remove(Cruise $cruise): void
    {
        if (!$cruise->delete()) {
            throw new \RuntimeException('Removing error', CruiseCodeException::CRUISE_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($cruise->releaseEvents());
    }
}
