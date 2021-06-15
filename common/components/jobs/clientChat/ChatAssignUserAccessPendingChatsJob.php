<?php

namespace common\components\jobs\clientChat;

use common\components\jobs\BaseJob;
use common\components\Metrics;
use sales\helpers\app\AppHelper;
use sales\repositories\NotFoundException;
use sales\services\clientChatService\ClientChatService;
use yii\queue\JobInterface;

/**
 * Class ChatAssignUserAccessPendingChatsJob
 * @package common\components\jobs\clientChat
 *
 * @property-read string $redisKey
 * @property-read int $userid
 */
class ChatAssignUserAccessPendingChatsJob extends BaseJob implements JobInterface
{
    private int $userId;

    private const REDIS_KEY = '-assign-user-access';

    private const REDIS_EXPIRE_S = 1800;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
        parent::__construct();
    }

    public function execute($queue)
    {
        try {
            \Yii::$app->redis->set($this->getRedisKey(), true);
            \Yii::$app->redis->expire($this->getRedisKey(), self::REDIS_EXPIRE_S);

            $service = \Yii::createObject(ClientChatService::class);
            $service->assignUserAccessToPendingChats($this->userId);
        } catch (NotFoundException $e) {
            \Yii::info($e->getMessage(), 'info\ChatAssignUserAccessPendingChats');
        } catch (\Throwable $e) {
            AppHelper::throwableLogger($e, 'ChatAssignUserAccessPendingChats:Execute:Throwable', false);
        }
        \Yii::$app->redis->del($this->getRedisKey());
    }

    private function getRedisKey(): string
    {
        return $this->userId . self::REDIS_KEY;
    }

    public function isRunning()
    {
        return \Yii::$app->redis->exists($this->getRedisKey());
    }
}
