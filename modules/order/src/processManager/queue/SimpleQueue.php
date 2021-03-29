<?php

namespace modules\order\src\processManager\queue;

/**
 * Class SimpleQueue
 *
 * @property \yii\queue\Queue $queue
 */
class SimpleQueue implements Queue
{
    private \yii\queue\Queue $queue;

    public function __construct(\yii\queue\Queue $queue)
    {
        $this->queue = $queue;
    }

    public function push($job): ?string
    {
        return $this->queue->push($job);
    }

    public function delay($value)
    {
        return $this->queue->delay($value);
    }

    public function priority($value)
    {
        return $this->queue->priority($value);
    }
}
