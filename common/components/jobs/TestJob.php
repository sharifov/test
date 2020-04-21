<?php
/**
 * Created by Alex Connor.
 * User: alexandr
 * Date: 2019-05-10
 */

namespace common\components\jobs;

use yii\base\BaseObject;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 * This is the Test JOB
 *
 * @property array $data
 */

class TestJob extends BaseObject implements JobInterface
{
    public $data = [];

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue) : bool
    {
        Yii::warning(VarDumper::dumpAsString($this->data), 'JOB:TestJob');
        return true;
    }

    public function getTtr()
    {
        return 1 * 20;
    }

    /*public function canRetry($attempt, $error)
    {
        return ($attempt < 5) && ($error instanceof TemporaryException);
    }*/
}