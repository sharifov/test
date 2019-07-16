<?php

namespace sales\jobs;

use yii\queue\JobInterface;

abstract class Job implements JobInterface
{
    public function execute($queue): void
    {
        $listener = $this->resolveHandler();
        $listener($this, $queue);
    }

    private function resolveHandler(): callable
    {
        return [\Yii::createObject(static::class . 'Handler'), 'handle'];
    }
}