<?php

use yii\db\Migration;

/**
 * Class m220713_131318_add_new_virtual_cron_command
 */
class m220713_131318_add_new_virtual_cron_command extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $cronScheduler = new \kivork\VirtualCron\Models\CronScheduler();
            $cronScheduler->cs_cron_expression = '*/10 * * * *';
            $cronScheduler->cs_cron_command = 'client/client-return-indication';
            $cronScheduler->cs_description = 'Client Return Indication';
            $cronScheduler->cs_cron_params = '1000';
            $cronScheduler->cs_hash = \kivork\VirtualCron\Services\Hasher::generate(
                $cronScheduler->cs_cron_expression,
                $cronScheduler->cs_cron_command,
                $cronScheduler->cs_cron_params
            );
            $cronScheduler->cs_enabled = false;

            if (!$cronScheduler->save()) {
                throw new \RuntimeException(\src\helpers\ErrorsToStringHelper::extractFromModel($cronScheduler, ' '));
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm220713_131318_add_new_virtual_cron_command:safeUp:Throwable');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $cs = \kivork\VirtualCron\Models\CronScheduler::find()->where(['cs_cron_command' => 'client/client-return-indication'])->one();
            if ($cs) {
                if ($cs->delete()) {
                    echo ' - Delete: OK' . PHP_EOL;
                } else {
                    Yii::error($cs->errors, 'm220713_131318_add_new_virtual_cron_command:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm220713_131318_add_new_virtual_cron_command:safeDown:Throwable');
        }
    }
}
