<?php
/**
 * Created by Alex Connor.
 * User: alexandr
 * Date: 2019-05-10
 */

namespace common\components\jobs;

use common\components\Metrics;
use yii\base\BaseObject;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 * This is the Test JOB
 *
 * @property-read float|int $ttr
 * @property array $data
 */

class TestJob extends BaseObject implements JobInterface
{
    public array $data = [];

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue) : bool
    {
        $metrics = new Metrics();
        $timeStart = microtime(true);

        Yii::warning(VarDumper::dumpAsString($this->data), 'JOB:TestJob');

        sleep(random_int(1, 5));
        $seconds = round(microtime(true) - $timeStart, 1);
        $metrics->jobHistogram(self::class, $seconds);
        return true;
    }

    /**
     * @return float|int
     */
    public function getTtr()
    {
        return 1 * 20;
    }

    /*public function canRetry($attempt, $error)
    {
        return ($attempt < 5) && ($error instanceof TemporaryException);
    }*/
}