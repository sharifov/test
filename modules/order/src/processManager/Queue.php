<?php

namespace modules\order\src\processManager;

/**
 * Class Queue
 *
 * @property \yii\queue\Queue $queue
 */
class Queue
{
    private \yii\queue\Queue $queue;

    public function __construct(\yii\queue\Queue $queue)
    {
        $this->queue = $queue;
    }

    public function push($job)
    {
        return $this->queue->push($job);
    }

    public function delay($value)
    {
        return $this->queue->delay($value);
    }
}
