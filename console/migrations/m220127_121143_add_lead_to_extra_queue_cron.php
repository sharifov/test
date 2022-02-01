<?php

use yii\db\Migration;

/**
 * Class m220127_121143_add_lead_to_extra_queue_cron
 */
class m220127_121143_add_lead_to_extra_queue_cron extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $cronScheduler = new \kivork\VirtualCron\Models\CronScheduler();
            $cronScheduler->cs_cron_expression = '*/5 * * * *';
            $cronScheduler->cs_cron_command = 'lead/to-extra-queue';
            $cronScheduler->cs_description = 'Lead to Extra queue';
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
            Yii::error($throwable, 'm220127_121143_add_lead_to_extra_queue_cron:safeUp:Throwable');
        }
    }

    /**
     * @return bool|void
     */
    public function safeDown()
    {
        try {
            $cronScheduler = \kivork\VirtualCron\Models\CronScheduler::find()->where(['cs_cron_command' => 'lead/to-extra-queue'])->one();
            if ($cronScheduler) {
                if ($cronScheduler->delete()) {
                    echo ' - Delete: OK' . PHP_EOL;
                } else {
                    Yii::error($cronScheduler->errors, 'm220127_121143_add_lead_to_extra_queue_cron:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm220127_121143_add_lead_to_extra_queue_cron:safeDown:Throwable');
        }
    }
}
