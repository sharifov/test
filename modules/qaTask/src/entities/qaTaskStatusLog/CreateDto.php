<?php

namespace modules\qaTask\src\entities\qaTaskStatusLog;

use modules\qaTask\src\entities\qaTask\QaTask;

/**
 * Class CreateDto
 *
 * @property QaTask $task
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
    public $task;
    public $startStatusId;
    public $endStatusId;
    public $reasonId;
    public $description;
    public $actionId;
    public $assignedId;
    public $creatorId;

    public function __construct(
        QaTask $task,
        ?int $startStatusId,
        int $endStatusId,
        ?int $reasonId,
        ?string $description,
        ?int $actionId,
        ?int $assignedId,
        ?int $creatorId
    )
    {
        $this->task = $task;
        $this->startStatusId = $startStatusId;
        $this->endStatusId = $endStatusId;
        $this->reasonId = $reasonId;
        $this->description = $description;
        $this->actionId = $actionId;
        $this->assignedId = $assignedId;
        $this->creatorId = $creatorId;
    }

    public function getTaskId(): int
    {
        return $this->task->t_id;
    }
}
