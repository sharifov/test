<?php

use yii\db\Migration;

/**
 * Class m220816_150420_add_cron_job_email_normalize
 */
class m220816_150420_add_cron_job_email_normalize extends Migration
{
    private const COMMAND = 'email/sync-normalized';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $cronScheduler = new \kivork\VirtualCron\Models\CronScheduler();
            $cronScheduler->cs_cron_expression = '0 * * * *';
            $cronScheduler->cs_cron_command = self::COMMAND;
            $cronScheduler->cs_description = 'Sync old email data into normalized form';
            $cronScheduler->cs_hash = \kivork\VirtualCron\Services\Hasher::generate(
                $cronScheduler->cs_cron_expression,
                $cronScheduler->cs_cron_command,
                $cronScheduler->cs_cron_params
            );
            $cronScheduler->cs_enabled = false;

            if (!$cronScheduler->save()) {
                throw new \RuntimeException(\src\helpers\ErrorsToStringHelper::extractFromModel($cronScheduler, ' '));
            }
            echo ' - Hash: ' . $cronScheduler->cs_hash . PHP_EOL;
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm220816_150420_add_cron_job_email_normalize:safeUp:Throwable');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $cs = \kivork\VirtualCron\Models\CronScheduler::find()->where(['cs_cron_command' => self::COMMAND])->one();
            if ($cs) {
                if ($cs->delete()) {
                    echo ' - Delete: OK' . PHP_EOL;
                } else {
                    Yii::error($cs->errors, 'm220816_150420_add_cron_job_email_normalize:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm220816_150420_add_cron_job_email_normalize:safeDown:Throwable');
        }
    }
}
