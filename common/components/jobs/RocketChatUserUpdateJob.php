<?php

namespace common\components\jobs;

use sales\helpers\app\AppHelper;
use yii\base\BaseObject;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 * @property float|int $ttr
 * @property string $userId
 * @property array $data
 */
class RocketChatUserUpdateJob extends BaseObject implements JobInterface
{
    public string $userId;
    public array $data;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue): bool
    {
        try {
            $rocketChat = \Yii::$app->rchat;
            $result = $rocketChat->updateUser($this->userId, $this->data);

            if (!empty($result['data'])) {
                Yii::info(
                    'RocketChat User Updated. ' .
                    VarDumper::dumpAsString($this->data, 10),
                    'info\RocketChatUserUpdateJob:execute:success'
                );
            }
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger($throwable, 'RocketChatUserUpdateJob:execute:Throwable');
        }
        return false;
    }

    /**
     * @return float|int
     */
    public function getTtr()
    {
        return 1 * 20;
    }
}
