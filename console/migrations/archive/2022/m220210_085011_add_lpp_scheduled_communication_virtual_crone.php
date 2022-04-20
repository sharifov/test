<?php

use yii\db\Migration;

/**
 * Class m220210_085011_add_lpp_scheduled_communication_virtual_crone
 */
class m220210_085011_add_lpp_scheduled_communication_virtual_crone extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $cronScheduler = new \kivork\VirtualCron\Models\CronScheduler();
            $cronScheduler->cs_cron_expression = '*/30 * * * *';
            $cronScheduler->cs_cron_command = 'lead/lpp-scheduled-communication';
            $cronScheduler->cs_description = 'Lead Poor Processing Scheduled Communication';
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
            Yii::error($throwable, 'm220210_085011_add_lpp_scheduled_communication_virtual_crone:safeUp:Throwable');
        }
    }

    /**
     * @return bool|void
     */
    public function safeDown()
    {
        try {
            $cronScheduler = \kivork\VirtualCron\Models\CronScheduler::find()->where(['cs_cron_command' => 'lead/lpp-scheduled-communication'])->one();
            if ($cronScheduler) {
                if ($cronScheduler->delete()) {
                    echo ' - Delete: OK' . PHP_EOL;
                } else {
                    Yii::error($cronScheduler->errors, 'm220210_085011_add_lpp_scheduled_communication_virtual_crone:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm220210_085011_add_lpp_scheduled_communication_virtual_crone:safeDown:Throwable');
        }
    }
}
