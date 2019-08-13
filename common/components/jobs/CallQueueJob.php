<?php
/**
 * Created by Alex Connor.
 * User: alexandr
 * Date: 2019-08-13
 */

namespace common\components\jobs;

use common\components\SearchService;
use common\models\Lead;
use yii\base\BaseObject;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 * This is the Job class for Call Queue.
 *
 * @property int $call_id
 */

class CallQueueJob extends BaseObject implements JobInterface
{
    public $call_id;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue) : bool
    {

        try {
            if($this->call_id) {

            }

        } catch (\Throwable $e) {
            Yii::error(VarDumper::dumpAsString($e->getMessage()), 'CallQueueJob:execute:catch');
        }
        return false;
    }

    /*public function getTtr()
    {
        return 1 * 60;
    }*/

    /*public function canRetry($attempt, $error)
    {
        return ($attempt < 5) && ($error instanceof TemporaryException);
    }*/
}