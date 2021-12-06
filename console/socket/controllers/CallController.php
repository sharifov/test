<?php

namespace console\socket\controllers;

use common\models\UserCallStatus;
use frontend\widgets\newWebPhone\DeviceHash;
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

        $now = date('Y-m-d H:i:s');
        $deviceId = null;
        $deviceHash = (string)$params['deviceHash']; // only from device page
        // todo validate hash, length, etc...
        if ($deviceHash) {
            if (!DeviceHash::isValid($deviceHash)) {
                return [
                    'cmd' => 'updateCurrentCalls',
                    'twilioDeviceError' => true,
                    'msg' => 'Device hash is invalid. Please refresh page!',
                    'hashIsInvalid' => true,
                ];
            }
            $device = PhoneDevice::find()->byHash($deviceHash)->byUserId($userId)->one();
            if ($device) {
                $deviceId = $device->pd_id;
            } else {
                try {
                    $device = PhoneDevice::create(
                        $userId,
                        $deviceHash,
                        PhoneDeviceNameGenerator::generate(),
                        PhoneDeviceIdentity::getClientId($userId, $deviceHash),
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

            if (!$device->pd_connection_id) {
                $device->updateConnectionId($connectionIdentity, $now);
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
