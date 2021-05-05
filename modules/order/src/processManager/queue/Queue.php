<?php

namespace modules\order\src\processManager\queue;

use yii\queue\JobInterface;

interface Queue
{
    /**
     * @param JobInterface|mixed $job
     * @return string|null id of a job message
     */
    public function push($job): ?string;

    /**
     * @param int|mixed $value
     * @return Queue
     */
    public function delay($value);

    /**
     * @param mixed $value
     * @return Queue
     */
    public function priority($value);
}
