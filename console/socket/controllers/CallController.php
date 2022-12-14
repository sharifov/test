<?php

namespace console\socket\controllers;

use common\models\UserCallStatus;
use src\model\call\services\currentQueueCalls\CurrentQueueCallsService;

/**
 * Class CallController
 *
 * @property CurrentQueueCallsService $currentQueueCallsService
 */
class CallController
{
    private CurrentQueueCallsService $currentQueueCallsService;

    public function __construct(CurrentQueueCallsService $currentQueueCallsService)
    {
        $this->currentQueueCallsService = $currentQueueCallsService;
    }

    public function actionGetCurrentQueueCalls($connectionIdentity, $params): array
    {
        if (!$connectionIdentity) {
            return [
                'errors' => [
                    'Connection Identity is empty. Refresh page.'
                ]
            ];
        }

        if (!isset($params['userId'])) {
            return [
                'errors' => [
                    'Not found user Id'
                ]
            ];
        }
        $userId = (int)$params['userId'];

        $generalLinePriorityIsEnabled = (bool)($params['generalLinePriorityIsEnabled'] ?? false);
        $finishedCallSid = null;
        if (isset($params['finishedCallSid'])) {
            $finishedCallSid = (string)$params['finishedCallSid'];
        }

        $userStatusType = UserCallStatus::find()->select(['us_type_id'])->where(['us_user_id' => $userId])->orderBy(['us_id' => SORT_DESC])->limit(1)->asArray()->one();
        $calls = $this->currentQueueCallsService->getQueuesCalls($userId, $finishedCallSid, $generalLinePriorityIsEnabled);

        return [
            'cmd' => 'updateCurrentCalls',
            'userId' => $userId,
            'data' => $calls->toArray(),
            'userStatus' => (int)($userStatusType['us_type_id'] ?? UserCallStatus::STATUS_TYPE_OCCUPIED),
        ];
    }

    public function __destruct()
    {
        unset($this->currentQueueCallsService);
    }
}
