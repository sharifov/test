<?php

namespace modules\fileStorage\src\entity\fileStorage\events;

/**
 * Class FileCreatedByLeadEvent
 *
 * @property int $leadId
 * @property string $name
 */
class FileCreatedByLeadEvent
{
    public int $leadId;
    public string $name;

    public function __construct(int $leadId, string $name)
    {
        $this->leadId = $leadId;
        $this->name = $name;
    }
}
