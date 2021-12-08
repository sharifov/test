<?php

namespace console\socket\controllers;

use common\models\UserCallStatus;
use sales\model\call\services\currentQueueCalls\CurrentQueueCallsService;
use sales\model\voip\phoneDevice\device\PhoneDevice;
use sales\model\voip\phoneDevice\device\PhoneDeviceIdentityGenerator;
use sales\model\voip\phoneDevice\device\PhoneDeviceNameGenerator;
use sales\model\voip\phoneDevice\device\RandomStringGenerator;

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
        $validateDeviceId = empty($params['validateDeviceId']) ? false : true; // only from device page
        if ($validateDeviceId) {
            $requestedDeviceId = empty($params['deviceId']) ? null : (int)$params['deviceId'];
            if ($requestedDeviceId) {
                $device = PhoneDevice::find()->byId($requestedDeviceId)->one();
                if (!$device) {
                    return [
                        'cmd' => 'updateCurrentCalls',
                        'error' => true,
                        'deviceIsInvalid' => true,
                        'msg' => 'Device not found. Please refresh page!',
                    ];
                }
                echo $device->pd_id . PHP_EOL;
                if (!$device->isEqualUser($userId)) {
                    return [
                        'cmd' => 'updateCurrentCalls',
                        'error' => true,
                        'deviceIsInvalid' => true,
                        'msg' => 'User is not owner of device. Please refresh page!',
                    ];
                }
                $deviceId = $device->pd_id;
            } else {
                try {
                    $devicePostfix = (new RandomStringGenerator())->generate(10);
                    $device = PhoneDevice::create(
                        $userId,
                        PhoneDeviceNameGenerator::generate($devicePostfix),
                        PhoneDeviceIdentityGenerator::generate($userId, $devicePostfix),
                        false,
                        false,
                        false,
                        null,
                        $now,
                        $now
                    );
                    unset($devicePostfix);
                    $device->save(false);
                    $deviceId = $device->pd_id;
                } catch (\Throwable $e) {
                    \Yii::error([
                        'message' => $e->getMessage(),
                        'userId' => $userId,
                    ], 'PhoneDevice:create');
                    return [
                        'cmd' => 'updateCurrentCalls',
                        'error' => true,
                        'deviceIsInvalid' => true,
                        'msg' => 'Device created error. Please refresh page!',
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
                    'error' => true,
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
