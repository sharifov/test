<?php

use yii\db\Migration;
use kivork\VirtualCron\Services\Hasher;
use kivork\VirtualCron\Models\CronScheduler;
use src\helpers\app\AppHelper;

/**
 * Class m220831_094807_add_cron_job_regenerate_default_sensitive_views
 */
class m220831_094807_add_cron_job_regenerate_default_sensitive_views extends Migration
{
    public const COMMAND = 'db/regenerate-default-sensitive-views';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $cronScheduler = new CronScheduler();
            $cronScheduler->cs_cron_expression = '* 2 * * *';
            $cronScheduler->cs_cron_command = self::COMMAND;
            $cronScheduler->cs_description = 'Regenerate views from db_data_sensitive (default record)';
            $cronScheduler->cs_enabled = false;
            $cronScheduler->cs_hash = Hasher::generate(
                $cronScheduler->cs_cron_expression,
                $cronScheduler->cs_cron_command,
                $cronScheduler->cs_cron_params
            );

            if (!$cronScheduler->save()) {
                throw new \RuntimeException(\src\helpers\ErrorsToStringHelper::extractFromModel($cronScheduler, ' '));
            }
            echo ' - Hash: ' . $cronScheduler->cs_hash . PHP_EOL;
        } catch (Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220831_094807_add_cron_job_regenerate_default_sensitive_views:safeUp:Throwable');
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
                    Yii::error($cs->errors, 'm220831_094807_add_cron_job_regenerate_default_sensitive_views:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220831_094807_add_cron_job_regenerate_default_sensitive_views:safeDown:Throwable');
        }
    }
}
