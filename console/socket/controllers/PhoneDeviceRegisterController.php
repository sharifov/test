<?php

namespace console\socket\controllers;

use common\models\UserConnection;
use sales\model\voip\phoneDevice\device\BuidValidator;
use sales\model\voip\phoneDevice\device\PhoneDevice;
use sales\model\voip\phoneDevice\device\RandomStringGenerator;

/**
 * Class PhoneDeviceRegisterController
 */
class PhoneDeviceRegisterController
{
    public function actionRegister($connectionIdentity, $params): array
    {
        $buidGenerated = false;
        if (empty($params['buid'])) {
            $stringGenerator = new RandomStringGenerator();
            $buid = $stringGenerator->generate(10);
            unset($stringGenerator);
            $buidGenerated = true;
        } else {
            $buid = $params['buid'];
            $buidValidator = new BuidValidator();
            if (!$buidValidator->validate($buid, $error)) {
                unset($buidValidator);
                return [
                    'cmd' => 'PhoneDeviceRegister',
                    'error' => true,
                    'buidIsInvalid' => true,
                    'msg' => 'Buid is invalid. ' . $error,
                ];
            }
            unset($buidValidator);
        }

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

        try {
            $device = PhoneDevice::new(
                $userConnection['uc_id'],
                $userConnection['uc_user_id'],
                $userConnection['uc_ip'],
                $userConnection['uc_user_agent'],
                date('Y-m-d H:i:s'),
                $buid
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
                'msg' => 'Device created error.',
            ];
        }

        return [
            'cmd' => 'PhoneDeviceRegister',
            'buid' => $buidGenerated ? $device->pd_buid : null,
            'userId' => $device->pd_user_id,
            'deviceId' => $device->pd_id,
            'devices' => PhoneDevice::find()->select(['pd_id'])
                ->byUserId($device->pd_user_id)
                ->andWhere(['!=', 'pd_id', $device->pd_id])
                ->andWhere(['IS NOT', 'pd_connection_id', null])
                ->andWhere(['pd_buid' => $device->pd_buid])
                ->orderBy(['pd_id' => SORT_ASC])
                ->column(),
        ];
    }
}
