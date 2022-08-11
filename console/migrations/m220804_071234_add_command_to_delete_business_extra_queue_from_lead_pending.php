<?php

use yii\db\Migration;

/**
 * Class m220804_071234_add_command_to_delete_business_extra_queue_from_lead_pending
 */
class m220804_071234_add_command_to_delete_business_extra_queue_from_lead_pending extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $cronScheduler = new \kivork\VirtualCron\Models\CronScheduler();
            $cronScheduler->cs_cron_expression = '*/10 * * * *';
            $cronScheduler->cs_cron_command = 'one-time/delete-business-extra-queue-from-pending-leads';
            $cronScheduler->cs_description = 'Delete Business Extra Queue From Pending Leads';
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
            Yii::error($throwable, 'm220804_071234_add_command_to_delete_business_extra_queue_from_lead_pending:safeUp:Throwable');
        }
    }

    /**
     * @return bool|void
     */
    public function safeDown()
    {
        try {
            $cronScheduler = \kivork\VirtualCron\Models\CronScheduler::find()->where(['cs_cron_command' => 'one-time/delete-business-extra-queue-from-pending-leads'])->one();
            if ($cronScheduler) {
                if ($cronScheduler->delete()) {
                    echo ' - Delete: OK' . PHP_EOL;
                } else {
                    Yii::error($cronScheduler->errors, 'm220804_071234_add_command_to_delete_business_extra_queue_from_lead_pending:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm220804_071234_add_command_to_delete_business_extra_queue_from_lead_pending:safeDown:Throwable');
        }
    }
}
