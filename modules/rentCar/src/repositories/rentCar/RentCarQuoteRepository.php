<?php

namespace modules\rentCar\src\repositories\rentCar;

use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use modules\rentCar\src\exceptions\RentCarCodeException;
use sales\dispatchers\EventDispatcher;

/**
 * Class RentCarQuoteRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class RentCarQuoteRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function save(RentCarQuote $model): int
    {
        if (!$model->save(false)) {
            throw new \RuntimeException('Saving error', RentCarCodeException::RENT_CAR_QUOTE_SAVE);
        }
        $this->eventDispatcher->dispatchAll($model->releaseEvents());
        return $model->rcq_id;
    }
}
