<?php

use yii\db\Migration;

/**
 * Class m220228_095607_add_old_leads_to_lpp_cron_command
 */
class m220228_095607_add_old_leads_to_lpp_cron_command extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $cronScheduler = new \kivork\VirtualCron\Models\CronScheduler();
            $cronScheduler->cs_cron_expression = '0 0 3 3 *';
            $cronScheduler->cs_cron_command = 'lead/to-last-action-lpp';
            $cronScheduler->cs_description = 'Lead Poor Processing Last Action Old Leads';
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
            Yii::error($throwable, 'm220228_095607_add_old_leads_to_lpp_cron_command:safeUp:Throwable');
        }
    }

    /**
     * @return bool|void
     */
    public function safeDown()
    {
        try {
            $cronScheduler = \kivork\VirtualCron\Models\CronScheduler::find()->where(['cs_cron_command' => 'lead/to-last-action-lpp'])->one();
            if ($cronScheduler) {
                if ($cronScheduler->delete()) {
                    echo ' - Delete: OK' . PHP_EOL;
                } else {
                    Yii::error($cronScheduler->errors, 'm220228_095607_add_old_leads_to_lpp_cron_command:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm220228_095607_add_old_leads_to_lpp_cron_command:safeDown:Throwable');
        }
    }
}
