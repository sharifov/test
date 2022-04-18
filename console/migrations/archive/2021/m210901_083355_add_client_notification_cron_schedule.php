<?php

use yii\db\Migration;

/**
 * Class m210901_083355_add_client_notification_cron_schedule
 */
class m210901_083355_add_client_notification_cron_schedule extends Migration
{
    /**
     * @return bool|void
     */
    public function safeUp()
    {
        try {
            $cs = new \kivork\VirtualCron\Models\CronScheduler();
            $cs->cs_cron_expression = '*/10 * * * *';
            $cs->cs_cron_command = 'client-notifications/notify';
            $cs->cs_description = 'Client notifications';
            $cs->cs_hash = \kivork\VirtualCron\Services\Hasher::generate(
                $cs->cs_cron_expression,
                $cs->cs_cron_command,
                $cs->cs_cron_params
            );
            $cs->cs_enabled = false;
            if ($cs->save()) {
                echo ' - Hash: ' . $cs->cs_hash;
            } else {
                Yii::error($cs->errors, 'm210901_083355_add_client_notification_cron_schedule:safeUp');
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm210901_083355_add_client_notification_cron_schedule:safeUp:Throwable');
        }
    }

    /**
     * @return bool|void
     */
    public function safeDown()
    {
        try {
            $cs = \kivork\VirtualCron\Models\CronScheduler::find()->where(['cs_cron_command' => 'client-notifications/notify'])->one();
            if ($cs) {
                if ($cs->delete()) {
                    echo ' - Delete: OK' . PHP_EOL;
                } else {
                    Yii::error($cs->errors, 'm210901_083355_add_client_notification_cron_schedule:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm210901_083355_add_client_notification_cron_schedule:safeDown:Throwable');
        }
    }
}
