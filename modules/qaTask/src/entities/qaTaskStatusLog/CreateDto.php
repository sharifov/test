<?php

namespace modules\qaTask\src\entities\qaTaskStatusLog;

/**
 * Class CreateDto
 *
 * @property $taskId
 * @property $startStatusId
 * @property $endStatusId
 * @property $reasonId
 * @property $description
 * @property $actionId
 * @property $assignedId
 * @property $creatorId
 */
class CreateDto
{
    public $taskId;
    public $startStatusId;
    public $endStatusId;
    public $reasonId;
    public $description;
    public $actionId;
    public $assignedId;
    public $creatorId;

    public function __construct(
        int $taskId,
        ?int $startStatusId,
        int $endStatusId,
        ?int $reasonId,
        ?string $description,
        ?int $actionId,
        ?int $assignedId,
        ?int $creatorId
    )
    {
        $this->taskId = $taskId;
        $this->startStatusId = $startStatusId;
        $this->endStatusId = $endStatusId;
        $this->reasonId = $reasonId;
        $this->description = $description;
        $this->actionId = $actionId;
        $this->assignedId = $assignedId;
        $this->creatorId = $creatorId;
    }
}
