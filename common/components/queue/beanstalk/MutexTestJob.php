<?php

namespace common\components\queue\beanstalk;

use yii\queue\JobInterface;

class MutexTestJob implements JobInterface
{
    private $i;

    public function __construct($i)
    {
        $this->i = $i;
    }

    public function execute($queue)
    {
       // usleep(100 * 1000);
        echo $this->i . PHP_EOL;
    }
}
