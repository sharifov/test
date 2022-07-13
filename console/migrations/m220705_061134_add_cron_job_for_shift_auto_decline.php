<?php

use yii\db\Migration;

/**
 * Class m220705_061134_add_cron_job_for_shift_auto_decline
 */
class m220705_061134_add_cron_job_for_shift_auto_decline extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $cronScheduler = new \kivork\VirtualCron\Models\CronScheduler();
            $cronScheduler->cs_cron_expression = '*/10 * * * *';
            $cronScheduler->cs_cron_command = 'shift/find-and-decline-shifts-with-pending-status-if-wt-or-wtr-exists';
            $cronScheduler->cs_description = 'Finds shifts with status pending that intersect with shifts with status BT and WTD  and closes them';
            $cronScheduler->cs_hash = \kivork\VirtualCron\Services\Hasher::generate(
                $cronScheduler->cs_cron_expression,
                $cronScheduler->cs_cron_command,
                $cronScheduler->cs_cron_params
            );
            $cronScheduler->cs_enabled = true;

            if (!$cronScheduler->save()) {
                throw new \RuntimeException(\src\helpers\ErrorsToStringHelper::extractFromModel($cronScheduler, ' '));
            }
            echo ' - Hash: ' . $cronScheduler->cs_hash . PHP_EOL;
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm220705_061134_add_cron_job_for_shift_auto_decline:safeUp:Throwable');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $cs = \kivork\VirtualCron\Models\CronScheduler::find()->where(['cs_cron_command' => 'shift/find-and-decline-shifts-with-pending-status-if-wt-or-wtr-exists'])->one();
            if ($cs) {
                if ($cs->delete()) {
                    echo ' - Delete: OK' . PHP_EOL;
                } else {
                    Yii::error($cs->errors, 'm220705_061134_add_cron_job_for_shift_auto_decline:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm220705_061134_add_cron_job_for_shift_auto_decline:safeDown:Throwable');
        }
    }
}
