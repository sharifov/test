<?php

namespace src\repositories;

use src\dispatchers\EventDispatcher;
use src\helpers\ErrorsToStringHelper;
use Yii;

/**
 * Class AbstractRepositoryWithEvent
 *
 * @property EventDispatcher|null $eventDispatcher
 */
abstract class AbstractRepositoryWithEvent extends AbstractBaseRepository
{
    private ?EventDispatcher $eventDispatcher = null;

    public function setEventDispatcher(EventDispatcher $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getEventDispatcher(): EventDispatcher
    {
        return $this->eventDispatcher ?? ($this->eventDispatcher = Yii::createObject(
            EventDispatcher::class
        ));
    }

    public function save(bool $runValidation = false, string $glue = ' '): AbstractBaseRepository
    {
        if (!$this->model->save($runValidation)) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($this->model, $glue));
        }
        if (method_exists($this->model, 'releaseEvents')) {
            $this->getEventDispatcher()->dispatchAll($this->model->releaseEvents());
        }
        return $this;
    }
}
