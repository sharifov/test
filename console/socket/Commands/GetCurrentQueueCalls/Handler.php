<?php

namespace console\socket\Commands\GetCurrentQueueCalls;

use common\models\UserCallStatus;
use sales\model\call\services\currentQueueCalls\CurrentQueueCallsService;

/**
 * Class Handler
 *
 * @property CurrentQueueCallsService $currentQueueCallsService
 */
class Handler
{
    private CurrentQueueCallsService $currentQueueCallsService;

    public function __construct(CurrentQueueCallsService $currentQueueCallsService)
    {
        $this->currentQueueCallsService = $currentQueueCallsService;
    }

    public function handle($params): array
    {
        if (!isset($params['userId'])) {
            return [
                'errors' => [
                    'Not found user Id'
                ]
            ];
        }

        $userId = (int)$params['userId'];
        $userStatusType = UserCallStatus::find()->select(['us_type_id'])->where(['us_user_id' => $userId])->orderBy(['us_id' => SORT_DESC])->limit(1)->asArray()->one();

        return [
            'cmd' => 'updateCurrentCalls',
            'userId' => $userId,
            'data' => $this->getCallsData($userId),
            'userStatus' => (int)($userStatusType['us_type_id'] ?? UserCallStatus::STATUS_TYPE_OCCUPIED),
        ];
    }

    private function getCallsData($userId): array
    {
        $calls = $this->currentQueueCallsService->getQueuesCalls($userId);
        $data = $calls->toArray();
        unset($calls);
        return $data;
    }

    public function __destruct()
    {
        unset($this->currentQueueCallsService);
    }
}
