<?php

namespace sales\model\leadRequest\repository;

use sales\helpers\ErrorsToStringHelper;
use sales\dispatchers\EventDispatcher;
use sales\model\leadRequest\entity\LeadRequest;

/**
 * Class LeadRequestRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class LeadRequestRepository
{
    private EventDispatcher $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function save(LeadRequest $model): int
    {
        if (!$model->save()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($model));
        }
        $this->eventDispatcher->dispatchAll($model->releaseEvents());
        return $model->lr_id;
    }
}
