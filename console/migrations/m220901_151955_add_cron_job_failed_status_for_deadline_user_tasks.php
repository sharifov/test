<?php

use yii\db\Migration;
use kivork\VirtualCron\Services\Hasher;
use kivork\VirtualCron\Models\CronScheduler;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;

/**
 * Class m220901_151955_add_cron_job_failed_status_for_deadline_user_tasks
 */
class m220901_151955_add_cron_job_failed_status_for_deadline_user_tasks extends Migration
{
    public const COMMAND = 'user-task/set-failed-statuses-for-deadlines';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $cronScheduler = new CronScheduler();
            $cronScheduler->cs_cron_expression = '5 * * * *';
            $cronScheduler->cs_cron_command = self::COMMAND;
            $cronScheduler->cs_description = 'Set "Failed" status for deadline user tasks';
            $cronScheduler->cs_enabled = false;
            $cronScheduler->cs_hash = Hasher::generate(
                $cronScheduler->cs_cron_expression,
                $cronScheduler->cs_cron_command,
                $cronScheduler->cs_cron_params
            );

            if (!$cronScheduler->save()) {
                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($cronScheduler, ' '));
            }
            echo ' - Hash: ' . $cronScheduler->cs_hash . PHP_EOL;
        } catch (Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220901_151955_add_cron_job_failed_status_for_deadline_user_tasks:safeUp:Throwable');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $cs = CronScheduler::find()->where(['cs_cron_command' => self::COMMAND])->one();
            if ($cs) {
                if ($cs->delete()) {
                    echo ' - Delete: OK' . PHP_EOL;
                } else {
                    Yii::error($cs->errors, 'm220901_151955_add_cron_job_failed_status_for_deadline_user_tasks:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220901_151955_add_cron_job_failed_status_for_deadline_user_tasks:safeDown:Throwable');
        }
    }
}
