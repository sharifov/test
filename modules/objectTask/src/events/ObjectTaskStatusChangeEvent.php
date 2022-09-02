<?php

namespace modules\objectTask\src\events;

class ObjectTaskStatusChangeEvent
{
    public string $objectTaskUuid;
    public int $newStatusId;
    public ?int $oldStatusId = null;
    public ?string $description = null;

    public function __construct(string $objectTaskUuid, int $newStatusId, ?int $oldStatusId = null, ?string $description = null)
    {
        $this->objectTaskUuid = $objectTaskUuid;
        $this->newStatusId = $newStatusId;
        $this->oldStatusId = $oldStatusId;
        $this->description = $description;
    }
}
