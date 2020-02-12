<?php

namespace modules\qaTask\src\useCases\qaTask\takeOver;

use modules\qaTask\src\entities\qaTask\QaTask;

/**
 * Class QaTaskActionTakeOverEvent
 *
 * @property QaTask $task
 * @property int $newAssignUserId
 * @property int|null $oldAssignedUserId
 * @property int|null $reasonId
 * @property string|null $description
 */
class QaTaskTakeOverEvent
{
    public $task;
    public $newAssignUserId;
    public $oldAssignedUserId;
    public $reasonId;
    public $description;

    public function __construct(
        QaTask $task,
        int $newAssignUserId,
        ?int $oldAssignedUserId,
        ?int $reasonId,
        ?string $description
    )
    {
        $this->task = $task;
        $this->newAssignUserId = $newAssignUserId;
        $this->oldAssignedUserId = $oldAssignedUserId;
        $this->reasonId = $reasonId;
        $this->description = $description;
    }
}
