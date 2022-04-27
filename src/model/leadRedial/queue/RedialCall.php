<?php

namespace src\model\leadRedial\queue;

/**
 * Class RedialCall
 *
 * @property int $userId
 * @property string $phoneFrom
 * @property int $phoneListId
 * @property string $phoneTo
 * @property int $projectId
 * @property int $projectName
 * @property int|null $departmentId
 * @property string|null $departmentName
 * @property int $leadId
 * @property int $clientId
 * @property string $clientName
 * @property bool $isClient
 */
class RedialCall
{
    public int $userId;
    public string $phoneFrom;
    public int $phoneListId;
    public string $phoneTo;
    public int $projectId;
    public string $projectName;
    public ?int $departmentId;
    public ?string $departmentName;
    public int $leadId;
    public int $clientId;
    public string $clientName;
    public bool $isClient;

    public function __construct(
        int $userId,
        string $phoneFrom,
        int $phoneListId,
        string $phoneTo,
        int $projectId,
        string $projectName,
        ?int $departmentId,
        ?string $departmentName,
        int $leadId,
        int $clientId,
        string $clientName,
        bool $isClient
    ) {
        $this->userId = $userId;
        $this->phoneFrom = $phoneFrom;
        $this->phoneListId = $phoneListId;
        $this->phoneTo = $phoneTo;
        $this->projectId = $projectId;
        $this->projectName = $projectName;
        $this->departmentId = $departmentId;
        $this->departmentName = $departmentName;
        $this->leadId = $leadId;
        $this->clientId = $clientId;
        $this->clientName = $clientName;
        $this->isClient = $isClient;
    }
}
