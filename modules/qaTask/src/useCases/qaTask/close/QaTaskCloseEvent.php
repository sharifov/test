<?php

namespace modules\qaTask\src\useCases\qaTask\close;

use modules\qaTask\src\entities\qaTask\QaTask;

/**
 * Class QaTaskCloseEvent
 *
 * @property QaTask $task
 * @property string|null $description
 */
class QaTaskCloseEvent
{
    public $task;
    public $description;

    public function __construct(
        QaTask $task,
        ?string $description
    )
    {
        $this->task = $task;
        $this->description = $description;
    }
}
