<?php

namespace src\model\voip\phoneDevice\log;

use yii\helpers\VarDumper;

class PhoneDeviceLogger
{
    public function log(int $userId, array $logs, \DateTimeImmutable $createdDt): void
    {
        $created = $createdDt->format('Y-m-d H:i:s');
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
                        'message' => 'Validation error.',
                        'logs' => $logs,
                        'key' => $key,
                        'log' => VarDumper::dumpAsString($log),
                        'errors' => $form->getErrors(),
                    ], 'PhoneDeviceLogger');
                    continue;
                }
                PhoneDeviceLog::getDb()->createCommand(
                    "insert into " . PhoneDeviceLog::tableName() . " 
                        (`pdl_user_id`, `pdl_device_id`, `pdl_level`, `pdl_message`, `pdl_error`, `pdl_stacktrace`, `pdl_timestamp_dt`, `pdl_created_dt`) 
                        values (:userId, :deviceId, :level, :message, :error, :stacktrace, :timestamp, :created)",
                    [
                        ':userId' => $userId,
                        ':deviceId' => $form->deviceId,
                        ':level' => $form->level,
                        ':message' => $form->getErrorMessage(),
                        ':error' => $form->getErrorObject(),
                        ':stacktrace' => $form->stacktrace,
                        ':timestamp' => $form->timestamp,
                        ':created' => $created
                    ]
                )->execute();
            } catch (\Throwable $e) {
                \Yii::error([
                    'e' => $e->getMessage(),
                    'message' => 'Saved log error.',
                    'logs' => $logs,
                    'key' => $key
                ], 'PhoneDeviceLogger');
            }
        }
    }
}
