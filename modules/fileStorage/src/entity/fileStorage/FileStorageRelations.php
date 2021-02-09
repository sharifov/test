<?php

namespace modules\fileStorage\src\entity\fileStorage;

/**
 * Class FileStorageRelations
 *
 * @property int|null $leadId
 * @property int|null $caseId
 * @property int|null $clientId
 */
class FileStorageRelations
{
    public ?int $leadId;
    public ?int $caseId;
    public ?int $clientId;

    public function __construct(?int $leadId, ?int $caseId, ?int $clientId)
    {
        $this->leadId = $leadId;
        $this->caseId = $caseId;
        $this->clientId = $clientId;
    }
}
