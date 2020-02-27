<?php

namespace modules\qaTask\src\entities\qaTask\events;

use modules\qaTask\src\entities\qaTask\QaTask;

/**
 * Class QaTaskChangeRatingEvent
 *
 * @property QaTask $task
 * @property int $rating
 */
class QaTaskChangeRatingEvent
{
    public $task;
    public $rating;

    public function __construct(QaTask $task, int $rating)
    {
        $this->task = $task;
        $this->rating = $rating;
    }
}
