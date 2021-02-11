<?php

namespace modules\rentCar\src\repositories\rentCar;

use modules\rentCar\src\entity\rentCar\RentCar;
use modules\rentCar\src\exceptions\RentCarCodeException;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

/**
 * Class RentCarRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class RentCarRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function save(RentCar $model): int
    {
        if (!$model->save(false)) {
            throw new \RuntimeException('Saving error', RentCarCodeException::RENT_CAR_SAVE);
        }
        $this->eventDispatcher->dispatchAll($model->releaseEvents());
        return $model->prc_id;
    }
}
