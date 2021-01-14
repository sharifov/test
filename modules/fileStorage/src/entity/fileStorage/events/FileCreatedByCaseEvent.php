<?php

namespace modules\fileStorage\src\entity\fileStorage\events;

/**
 * Class FileCreatedByCaseEvent
 *
 * @property int $caseId
 * @property string $name
 */
class FileCreatedByCaseEvent
{
    public int $caseId;
    public string $name;

    public function __construct(int $caseId, string $name)
    {
        $this->caseId = $caseId;
        $this->name = $name;
    }
}
