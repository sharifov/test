<?php

namespace console\socket\controllers;

use common\models\UserConnection;
use sales\model\voip\phoneDevice\device\PhoneDevice;

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
        $userConnection = UserConnection::find()->select(['uc_id', 'uc_ip', 'uc_user_id', 'uc_user_agent'])->byId($connectionIdentity)->asArray()->one();
        if (!$userConnection) {
            return [
                'cmd' => 'PhoneDeviceRegister',
                'error' => true,
                'msg' => 'User connection is invalid. Please refresh page!',
            ];
        }

        $now = date('Y-m-d H:i:s');

        $requestedDeviceId = empty($params['deviceId']) ? null : (int)$params['deviceId'];
        if ($requestedDeviceId) {
            return $this->withRequestedDeviceId($requestedDeviceId, $userConnection, $now);
        }

        return $this->withNewDevice($userConnection, $now);
    }

    private function withNewDevice(array $userConnection, string $now): array
    {
        try {
            $device = PhoneDevice::new(
                $userConnection['uc_id'],
                $userConnection['uc_user_id'],
                $userConnection['uc_ip'],
                $userConnection['uc_user_agent'],
                $now
            );
            $device->save(false);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'userId' => $userConnection['uc_user_id'],
            ], 'PhoneDevice:create');
            return [
                'cmd' => 'PhoneDeviceRegister',
                'error' => true,
                'deviceIsInvalid' => true,
                'msg' => 'Device created error. Please refresh page!',
            ];
        }

        return [
            'cmd' => 'PhoneDeviceRegister',
            'userId' => $device->pd_user_id,
            'deviceId' => $device->pd_id,
        ];
    }

    private function withRequestedDeviceId(int $requestedDeviceId, array $userConnection, string $now): array
    {
        $device = PhoneDevice::find()->byId($requestedDeviceId)->one();
        if (!$device) {
            return [
                'cmd' => 'PhoneDeviceRegister',
                'error' => true,
                'deviceIsInvalid' => true,
                'msg' => 'Device not found. Please refresh page!',
            ];
        }
        if (!$device->isEqualUser((int)$userConnection['uc_user_id'])) {
            return [
                'cmd' => 'PhoneDeviceRegister',
                'error' => true,
                'deviceIsInvalid' => true,
                'msg' => 'User is not owner of device. Please refresh page!',
            ];
        }

        if (!$device->pd_connection_id) {
            $device->updateConnection($userConnection['uc_id'], $userConnection['uc_ip'], $now);
            $device->save(false);
        }

        if (!$device->isEqualConnection((int)$userConnection['uc_id'])) {
            return [
                'cmd' => 'PhoneDeviceRegister',
                'error' => true,
                'errorType' => 'voipPageAlreadyOpened',
            ];
        }

        return [
            'cmd' => 'PhoneDeviceRegister',
            'userId' => $device->pd_user_id,
            'deviceId' => $device->pd_id,
        ];
    }
}
