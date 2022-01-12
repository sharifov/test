<?php

namespace src\entities\cases\events;

use src\entities\cases\Cases;

/**
 * Class CasesAwaitingStatusEvent
 *
 * @property Cases $case
 * @property int $oldStatus
 * @property int|null $ownerId
 * @property int|null $creatorId
 * @property string|null $description
 */
class CasesAwaitingStatusEvent
{
    public $case;
    public $oldStatus;
    public $ownerId;
    public $creatorId;
    public $description;

    public function __construct(Cases $case, ?int $oldStatus, ?int $ownerId, ?int $creatorId, ?string $description)
    {
        $this->case = $case;
        $this->oldStatus = $oldStatus;
        $this->ownerId = $ownerId;
        $this->creatorId = $creatorId;
        $this->description = $description;
    }
}
