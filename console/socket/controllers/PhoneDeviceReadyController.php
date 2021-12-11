<?php

namespace console\socket\controllers;

use sales\model\voip\phoneDevice\device\PhoneDevice;

/**
 * Class PhoneDeviceReadyController
 */
class PhoneDeviceReadyController
{
    public function actionTwilioReady($connectionIdentity, $params): array
    {
        $action = 'Twilio ready';

        $result = $this->process($connectionIdentity, $params, $action);
        if ($result['error']) {
            return $result['error'];
        }

        /** @var PhoneDevice $device */
        $device = $result['device'];

        try {
            $device->deviceReady(date('Y-m-d H:i:s'));
            $device->save(false);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'userId' => $device->pd_user_id,
                'deviceId' => $device->pd_id,
            ], 'ws:PhoneDeviceReadyController:actionTwilioReady');
            return [
                'cmd' => 'PhoneDeviceReady',
                'error' => true,
                'msg' => [
                    'name' => 'PhoneDeviceReady. Action: ' . $action,
                    'message' => 'Twilio device ready registered error. Please refresh page!',
                ],
            ];
        }

        return [
//            'cmd' => 'PhoneDeviceReady',
//            'error' => false,
//            'msg' => $action . '. OK',
        ];
    }

    public function actionTwilioNotReady($connectionIdentity, $params): array
    {
        $action = 'Twilio not ready';

        $result = $this->process($connectionIdentity, $params, $action);
        if ($result['error']) {
            return $result['error'];
        }

        /** @var PhoneDevice $device */
        $device = $result['device'];

        try {
            $device->deviceNotReady(date('Y-m-d H:i:s'));
            $device->save(false);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'userId' => $device->pd_user_id,
                'deviceId' => $device->pd_id,
            ], 'ws:PhoneDeviceReadyController:actionTwilioNotReady');
            return [
                'cmd' => 'PhoneDeviceReady',
                'error' => true,
                'msg' => [
                    'name' => 'PhoneDeviceReady. Action: ' . $action,
                    'message' => 'Twilio device not ready registered error. Please refresh page!',
                ],
            ];
        }

        return [
//            'cmd' => 'PhoneDeviceReady',
//            'error' => false,
//            'msg' => $action . '. OK',
        ];
    }

    public function actionSpeakerReady($connectionIdentity, $params): array
    {
        $action = 'Speaker ready';

        $result = $this->process($connectionIdentity, $params, $action);
        if ($result['error']) {
            return $result['error'];
        }

        /** @var PhoneDevice $device */
        $device = $result['device'];

        try {
            $device->speakerReady(date('Y-m-d H:i:s'));
            $device->save(false);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'userId' => $device->pd_user_id,
                'deviceId' => $device->pd_id,
            ], 'ws:PhoneDeviceReadyController:actionSpeakerReady');
            return [
                'cmd' => 'PhoneDeviceReady',
                'error' => true,
                'msg' => [
                    'name' => 'PhoneDeviceReady. Action: ' . $action,
                    'message' => 'Speaker device ready registered error. Please refresh page!',
                ],
            ];
        }

        return [
//            'cmd' => 'PhoneDeviceReady',
//            'error' => false,
//            'msg' => $action . '. OK',
        ];
    }

    public function actionSpeakerNotReady($connectionIdentity, $params): array
    {
        $action = 'Speaker not ready';

        $result = $this->process($connectionIdentity, $params, $action);
        if ($result['error']) {
            return $result['error'];
        }

        /** @var PhoneDevice $device */
        $device = $result['device'];

        try {
            $device->speakerNotReady(date('Y-m-d H:i:s'));
            $device->save(false);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'userId' => $device->pd_user_id,
                'deviceId' => $device->pd_id,
            ], 'ws:PhoneDeviceReadyController:actionSpeakerNotReady');
            return [
                'cmd' => 'PhoneDeviceReady',
                'error' => true,
                'msg' => [
                    'name' => 'PhoneDeviceReady. Action: ' . $action,
                    'message' => 'Speaker device not ready registered error. Please refresh page!',
                ],
            ];
        }

        return [
//            'cmd' => 'PhoneDeviceReady',
//            'error' => false,
//            'msg' => $action . '. OK',
        ];
    }

    public function actionMicrophoneReady($connectionIdentity, $params): array
    {
        $action = 'Microphone ready';

        $result = $this->process($connectionIdentity, $params, $action);
        if ($result['error']) {
            return $result['error'];
        }

        /** @var PhoneDevice $device */
        $device = $result['device'];

        try {
            $device->microphoneReady(date('Y-m-d H:i:s'));
            $device->save(false);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'userId' => $device->pd_user_id,
                'deviceId' => $device->pd_id,
            ], 'ws:PhoneDeviceReadyController:actionMicrophoneReady');
            return [
                'cmd' => 'PhoneDeviceReady',
                'error' => true,
                'msg' => [
                    'name' => 'PhoneDeviceReady. Action: ' . $action,
                    'message' => 'Microphone device ready registered error. Please refresh page!',
                ],
            ];
        }

        return [
//            'cmd' => 'PhoneDeviceReady',
//            'error' => false,
//            'msg' => $action . '. OK',
        ];
    }

    public function actionMicrophoneNotReady($connectionIdentity, $params): array
    {
        $action = 'Microphone not ready';

        $result = $this->process($connectionIdentity, $params, $action);
        if ($result['error']) {
            return $result['error'];
        }

        /** @var PhoneDevice $device */
        $device = $result['device'];

        try {
            $device->microphoneNotReady(date('Y-m-d H:i:s'));
            $device->save(false);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'userId' => $device->pd_user_id,
                'deviceId' => $device->pd_id,
            ], 'ws:PhoneDeviceReadyController:actionMicrophoneNotReady');
            return [
                'cmd' => 'PhoneDeviceReady',
                'error' => true,
                'msg' => [
                    'name' => 'PhoneDeviceReady. Action: ' . $action,
                    'message' => 'Microphone device not ready registered error. Please refresh page!',
                ],
            ];
        }

        return [
//            'cmd' => 'PhoneDeviceReady',
//            'error' => false,
//            'msg' => $action . '. OK',
        ];
    }

    private function process($connectionIdentity, $params, $action): array
    {
        if (!$connectionIdentity) {
            return [
                'error' => [
                    'cmd' => 'PhoneDeviceReady',
                    'error' => true,
                    'msg' => [
                        'name' => 'PhoneDeviceReady. Action: ' . $action,
                        'message' => 'Connection Identity is empty',
                    ],
                ],
            ];
        }

        if (!isset($params['userId'])) {
            return [
                'error' => [
                    'cmd' => 'PhoneDeviceReady',
                    'error' => true,
                    'msg' => [
                        'name' => 'PhoneDeviceReady. Action: ' . $action,
                        'message' => 'Not found User Id',
                    ],
                ],
            ];
        }

        if (!isset($params['deviceId'])) {
            return [
                'error' => [
                    'cmd' => 'PhoneDeviceReady',
                    'error' => true,
                    'msg' => [
                        'name' => 'PhoneDeviceReady. Action: ' . $action,
                        'message' => 'Not found Device Id',
                    ],
                ],
            ];
        }

        $userId = (int)$params['userId'];
        $deviceId = (int)$params['deviceId'];

        $device = PhoneDevice::find()->byId($deviceId)->one();
        if (!$device) {
            return [
                'error' => [
                    'cmd' => 'PhoneDeviceReady',
                    'error' => true,
                    'deviceIsInvalid' => true,
                    'msg' => [
                        'name' => 'PhoneDeviceReady. Action: ' . $action,
                        'message' => 'Not found Device. ID: (' . $deviceId . '). Please, refresh Voip page!',
                    ],
                ],
            ];
        }

        if (!$device->isEqualUser($userId)) {
            return [
                'error' => [
                    'cmd' => 'PhoneDeviceReady',
                    'error' => true,
                    'deviceIsInvalid' => true,
                    'msg' => [
                        'name' => 'PhoneDeviceReady. Action: ' . $action,
                        'message' => 'User is not owner of device. Please, refresh Voip page!',
                    ],
                ],
            ];
        }

        return ['error' => false, 'device' => $device];
    }
}
