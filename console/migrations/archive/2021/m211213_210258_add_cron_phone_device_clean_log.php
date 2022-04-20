<?php

use yii\db\Migration;

/**
 * Class m211213_210258_add_cron_phone_device_clean_log
 */
class m211213_210258_add_cron_phone_device_clean_log extends Migration
{
    public function safeUp()
    {
        try {
            $cs = new \kivork\VirtualCron\Models\CronScheduler();
            $cs->cs_cron_expression = '0 3 * * *';
            $cs->cs_cron_command = 'voip/clean-phone-device-log';
            $cs->cs_description = 'Clean phone device log';
            $cs->cs_hash = \kivork\VirtualCron\Services\Hasher::generate(
                $cs->cs_cron_expression,
                $cs->cs_cron_command,
                $cs->cs_cron_params
            );
            $cs->cs_enabled = true;
            if ($cs->save()) {
                echo ' - Hash: ' . $cs->cs_hash;
            } else {
                Yii::error($cs->errors, 'm211213_210258_add_cron_phone_device_clean_log:safeUp');
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm211213_210258_add_cron_phone_device_clean_log:safeUp:Throwable');
        }
    }

    public function safeDown()
    {
        try {
            $cs = \kivork\VirtualCron\Models\CronScheduler::find()->where(['cs_cron_command' => 'voip/clean-phone-device-log'])->one();
            if ($cs) {
                if ($cs->delete()) {
                    echo ' - Delete: OK' . PHP_EOL;
                } else {
                    Yii::error($cs->errors, 'm211213_210258_add_cron_phone_device_clean_log:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm211213_210258_add_cron_phone_device_clean_log:safeDown:Throwable');
        }
    }
}
