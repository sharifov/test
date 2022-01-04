<?php

namespace sales\model\leadRedial\queue;

/**
 * Class RedialCall
 *
 * @property int $userId
 * @property string $phoneFrom
 * @property int $phoneListId
 * @property string $phoneTo
 * @property int $projectId
 * @property int|null $departmentId
 * @property int $leadId
 * @property int $clientId
 */
class RedialCall
{
    public int $userId;
    public string $phoneFrom;
    public int $phoneListId;
    public string $phoneTo;
    public int $projectId;
    public ?int $departmentId;
    public int $leadId;
    public int $clientId;

    public function __construct(
        int $userId,
        string $phoneFrom,
        int $phoneListId,
        string $phoneTo,
        int $projectId,
        ?int $departmentId,
        int $leadId,
        int $clientId
    ) {
        $this->userId = $userId;
        $this->phoneFrom = $phoneFrom;
        $this->phoneListId = $phoneListId;
        $this->phoneTo = $phoneTo;
        $this->projectId = $projectId;
        $this->departmentId = $departmentId;
        $this->leadId = $leadId;
        $this->clientId = $clientId;
    }
}
