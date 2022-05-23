<?php

namespace common\components\jobs\clientChat;

use common\components\jobs\BaseJob;
use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
use src\model\clientChat\entity\ClientChat;
use src\repositories\NotFoundException;
use src\services\clientChatService\ClientChatService;

/**
 * Class ClientChatUserAccessJob
 * @package common\components\jobs\clientChat
 *
 * @property int $chatId
 */
class ClientChatUserAccessJob extends BaseJob implements \yii\queue\JobInterface
{
    public $chatId = 0;
    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $this->waitingTimeRegister();
        $service = \Yii::createObject(ClientChatService::class);

        try {
            $key = ClientChatService::getRedisDistributionLogicKey($this->chatId);

            if (!$chat = ClientChat::findOne($this->chatId)) {
                throw new NotFoundException('Chat not found by id: ' . $this->chatId);
            }

            if (SettingHelper::isClientChatDebugEnable() && $chat->isTransfer()) {
                \Yii::info([
                    'message' => 'ClientChatUserAccessJob started for transfer chat',
                    'chatId' => $this->chatId,
                    'microTime' => microtime(true),
                    'date' => date('Y-m-d H:i:s'),
                ], 'info\ClientChatDebug');
            }

            if (!$chat->isPending() && !$chat->isTransfer() && !$chat->isIdle()) {
                if (SettingHelper::isClientChatDebugEnable()) {
                    \Yii::info([
                        'message' => 'ClientChatUserAccessJob exit because chat is not status (pending, transfer, idle)',
                        'chatId' => $this->chatId,
                        'microTime' => microtime(true),
                        'date' => date('Y-m-d H:i:s'),
                    ], 'info\ClientChatDebug');
                }
                \Yii::$app->redis->del($key);
                return;
            }

            $service->sendRequestToUsers($chat);
        } catch (NotFoundException $e) {
            \Yii::info($e->getMessage(), 'info\ClientChatUserAccessJob');
        } catch (\Throwable $e) {
            AppHelper::throwableLogger($e, 'ClientChatUserAccessJob:Execute:Throwable', false);
        }
    }
}
