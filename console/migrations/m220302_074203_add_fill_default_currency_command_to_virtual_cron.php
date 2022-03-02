<?php

use yii\db\Migration;

/**
 * Class m220302_074203_add_fill_default_currency_command_to_virtual_cron
 */
class m220302_074203_add_fill_default_currency_command_to_virtual_cron extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $cronScheduler = new \kivork\VirtualCron\Models\CronScheduler();
            $cronScheduler->cs_cron_expression = '*/10 * * * *';
            $cronScheduler->cs_cron_command = 'one-time/fill-default-currency-in-lead-preferences';
            $cronScheduler->cs_description = 'Fill default Currency in lead Preferences';
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
            Yii::error($throwable, 'm220302_074203_add_fill_default_currency_command_to_virtual_cron:safeUp:Throwable');
        }
    }

    /**
     * @return bool|void
     */
    public function safeDown()
    {
        try {
            $cronScheduler = \kivork\VirtualCron\Models\CronScheduler::find()->where(['cs_cron_command' => 'one-time/fill-default-currency-in-lead-preferences'])->one();
            if ($cronScheduler) {
                if ($cronScheduler->delete()) {
                    echo ' - Delete: OK' . PHP_EOL;
                } else {
                    Yii::error($cronScheduler->errors, 'm220302_074203_add_fill_default_currency_command_to_virtual_cron:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm220302_074203_add_fill_default_currency_command_to_virtual_cron:safeDown:Throwable');
        }
    }
}
