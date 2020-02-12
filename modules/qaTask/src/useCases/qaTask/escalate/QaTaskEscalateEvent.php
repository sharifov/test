<?php

namespace modules\qaTask\src\useCases\qaTask\escalate;

use modules\qaTask\src\entities\qaTask\QaTask;

/**
 * Class QaTaskEscalateEvent
 *
 * @property QaTask $task
 * @property int|null $reasonId
 * @property string|null $description
 */
class QaTaskEscalateEvent
{
    public $task;
    public $reasonId;
    public $description;

    public function __construct(
        QaTask $task,
        ?int $reasonId,
        ?string $description
    )
    {
        $this->task = $task;
        $this->reasonId = $reasonId;
        $this->description = $description;
    }
}
