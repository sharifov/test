<?php

namespace console\socket\controllers;

use common\models\UserConnection;
use sales\model\voip\phoneDevice\device\PhoneDevice;
use sales\model\voip\phoneDevice\device\PhoneDeviceIdentityGenerator;
use sales\model\voip\phoneDevice\device\PhoneDeviceNameGenerator;
use sales\model\voip\phoneDevice\device\RandomStringGenerator;

/**
 * Class PhoneDeviceRegisterController
 */
class PhoneDeviceRegisterController
{
    public function actionRegister($connectionIdentity, $params): array
    {
        if (!$connectionIdentity) {
            return [
                'cmd' => 'PhoneDeviceRegister',
                'error' => true,
                'msg' => 'Connection Identity is empty. Refresh page.',
            ];
        }

        if (!isset($params['userId'])) {
            return [
                'cmd' => 'PhoneDeviceRegister',
                'error' => true,
                'msg' => 'Not found User Id',
            ];
        }
        $userId = (int)$params['userId'];

        $now = date('Y-m-d H:i:s');

        $requestedDeviceId = empty($params['deviceId']) ? null : (int)$params['deviceId'];
        if ($requestedDeviceId) {
            $device = PhoneDevice::find()->byId($requestedDeviceId)->one();
            if (!$device) {
                return [
                    'cmd' => 'PhoneDeviceRegister',
                    'error' => true,
                    'deviceIsInvalid' => true,
                    'msg' => 'Device not found. Please refresh page!',
                ];
            }
            if (!$device->isEqualUser($userId)) {
                return [
                    'cmd' => 'PhoneDeviceRegister',
                    'error' => true,
                    'deviceIsInvalid' => true,
                    'msg' => 'User is not owner of device. Please refresh page!',
                ];
            }
        } else {
            try {
                $userConnection = UserConnection::find()->select(['uc_ip', 'uc_user_agent'])->byId($connectionIdentity)->asArray()->one();
                if (!$userConnection) {
                    return [
                        'cmd' => 'PhoneDeviceRegister',
                        'error' => true,
                        'deviceIsInvalid' => true,
                        'msg' => 'User connection is invalid. Please refresh page!',
                    ];
                }
                $devicePostfix = (new RandomStringGenerator())->generate(10);
                $device = PhoneDevice::create(
                    $userId,
                    PhoneDeviceNameGenerator::generate($devicePostfix),
                    PhoneDeviceIdentityGenerator::generate($userId, $devicePostfix),
                    false,
                    false,
                    false,
                    $userConnection['uc_ip'],
                    $userConnection['uc_user_agent'],
                    $now,
                    $now
                );
                unset($devicePostfix, $userConnection);
                $device->save(false);
            } catch (\Throwable $e) {
                \Yii::error([
                    'message' => $e->getMessage(),
                    'userId' => $userId,
                ], 'PhoneDevice:create');
                return [
                    'cmd' => 'PhoneDeviceRegister',
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
                'cmd' => 'PhoneDeviceRegister',
                'error' => true,
                'msg' => 'Voip page is already opened. Please close this page!',
            ];
        }

        return [
            'cmd' => 'PhoneDeviceRegister',
            'userId' => $userId,
            'deviceId' => $device->pd_id,
        ];
    }
}
