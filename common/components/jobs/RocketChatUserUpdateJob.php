<?php

namespace common\components\jobs;

use src\helpers\app\AppHelper;
use src\model\userClientChatData\entity\UserClientChatData;
use yii\base\BaseObject;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 * @property float|int $ttr
 * @property string $userId
 * @property array $data
 * @property int $userClientChatDataId
 */
class RocketChatUserUpdateJob extends BaseJob implements JobInterface
{
    public string $userId;
    public array $data;
    public int $userClientChatDataId;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue): bool
    {
        $this->waitingTimeRegister();
        $this->setTimeExecution(microtime(true));
        try {
            $rocketChat = \Yii::$app->rchat;
            $result = $rocketChat->updateUser($this->userId, $this->data);

            if (!empty($result['data'])) {
                Yii::info(
                    'RocketChat User Updated. ' .
                    VarDumper::dumpAsString($this->data, 10),
                    'info\RocketChatUserUpdateJob:execute:success'
                );

                if (!empty($this->data['name']) && $userClientChatData = UserClientChatData::findOne($this->userClientChatDataId)) {
                    $userClientChatData->uccd_name = $this->data['name'];
                    if (!$userClientChatData->save()) {
                        Yii::error($userClientChatData->getErrorSummary(true)[0], 'RocketChatUserUpdateJob::userClientChatData::update');
                    }
                }
            }
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger($throwable, 'RocketChatUserUpdateJob:execute:Throwable');
        }

        $this->execTimeRegister();

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
