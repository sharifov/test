<?php

namespace sales\model\voip\phoneDevice;

use yii\helpers\VarDumper;

class PhoneDeviceLogger
{
    public function log(int $userId, array $logs): void
    {
        $now = date('Y-m-d H:i:s');
        foreach ($logs as $key => $log) {
            try {
                $form = new PhoneDeviceLogForm();
                if (!$form->load($log)) {
                    \Yii::error([
                        'message' => 'Cant load log',
                        'logs' => $logs,
                        'key' => $key,
                        'log' => VarDumper::dumpAsString($log),
                    ], 'PhoneDeviceLogger');
                    continue;
                }
                if (!$form->validate()) {
                    \Yii::error([
                        'message' => 'Log is invalid',
                        'logs' => $logs,
                        'key' => $key,
                        'log' => VarDumper::dumpAsString($log),
                        'errors' => $form->getErrors(),
                    ], 'PhoneDeviceLogger');
                    continue;
                }
                VarDumper::dump($form->getAttributes());
//                die;
//                $log = PhoneDeviceLog::create($userId, $deviceId, $level, $message, $error,  $timestamp, $now);
//                $log->save(false);
            } catch (\Throwable $e) {
                \Yii::error([
                    'e' => $e->getMessage(),
                    'message' => 'Log is invalid',
                    'logs' => $logs,
                    'key' => $key
                ], 'PhoneDeviceLogger');
            }
        }
    }

    private function validateLog($log): void
    {
    }
}
