<?php

namespace console\socket\controllers;

use common\models\UserCallStatus;
use common\models\UserConnection;
use sales\model\call\services\currentQueueCalls\CurrentQueueCallsService;
use sales\model\voip\phoneDevice\device\PhoneDevice;
use sales\model\voip\phoneDevice\device\PhoneDeviceIdentity;
use sales\model\voip\phoneDevice\device\PhoneDeviceNameGenerator;

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

        $deviceHash = (string)$params['deviceHash'];
        if (!$deviceHash) {
            return [
                'errors' => [
                    'Not found device hash'
                ]
            ];
        }
        // todo validate hash (length...)

        $deviceId = null;
        $device = PhoneDevice::find()->byHash($deviceHash)->one();
        if ($device) {
            if ($device->pd_user_id !== $userId) {
                \Yii::error([
                    'message' => 'Found different users with equal device hash',
                    'existUserId' => $device->pd_user_id,
                    'requestedUserId' => $userId,
                    'hash' => $deviceHash,
                ], 'PhoneDevice:hash');
                return [
                    'errors' => [
                        'Device hash is invalid. Contact to administrator.'
                    ]
                ];
            }
            $deviceId = $device->pd_id;
        } else {
            try {
                $now = date('Y-m-d H:i:s');
                $device = PhoneDevice::create(
                    $userId,
                    $deviceHash,
                    PhoneDeviceNameGenerator::generate(),
                    PhoneDeviceIdentity::getId($userId, $deviceHash),
                    false,
                    false,
                    false,
                    null,
                    $now,
                    $now
                );
                $device->save(false);
                $deviceId = $device->pd_id;
            } catch (\Throwable $e) {
                \Yii::error([
                    'message' => $e->getMessage(),
                    'userId' => $userId,
                ], 'PhoneDevice:create');
                return [
                    'errors' => [
                        'Device created error. Refresh page.'
                    ]
                ];
            }
        }

        if (isset($params['isTwilioDevicePage']) && (bool)$params['isTwilioDevicePage']) {
            if (!$device->pd_connection_id) {
                $device->updateConnectionId($connectionIdentity);
                $device->save(false);
            }
            if (!$device->isEqualConnection($connectionIdentity)) {
                return [
                    'cmd' => 'updateCurrentCalls',
                    'twilioDeviceError' => true,
                    'msg' => 'Voip page is already opened. Please close this page!',
                ];
            }
        }

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
            'deviceId' => $deviceId,
        ];
    }

    public function __destruct()
    {
        unset($this->currentQueueCallsService);
    }
}
