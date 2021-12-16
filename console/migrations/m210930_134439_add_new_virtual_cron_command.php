<?php

use yii\db\Migration;

/**
 * Class m210930_134439_add_new_virtual_cron_command
 */
class m210930_134439_add_new_virtual_cron_command extends Migration
{
    public function safeUp()
    {
        try {
            $cs = new \kivork\VirtualCron\Models\CronScheduler();
            $cs->cs_cron_expression = '0 0 * * *';
            $cs->cs_cron_command = 'user/calculate-gross-profit';
            $cs->cs_description = 'Calculate users gross profit and save in to UserStatDay table';
            $cs->cs_hash = \kivork\VirtualCron\Services\Hasher::generate(
                $cs->cs_cron_expression,
                $cs->cs_cron_command,
                $cs->cs_cron_params
            );
            $cs->cs_enabled = false;
            if ($cs->save()) {
                echo ' - Hash: ' . $cs->cs_hash;
            } else {
                Yii::error($cs->errors, 'm210930_134439_add_new_virtual_cron_command:safeUp');
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm210930_134439_add_new_virtual_cron_command:safeUp:Throwable');
        }
    }

    public function safeDown()
    {
        try {
            $cs = \kivork\VirtualCron\Models\CronScheduler::find()->where(['cs_cron_command' => 'user/calculate-gross-profit'])->one();
            if ($cs) {
                if ($cs->delete()) {
                    echo ' - Delete: OK' . PHP_EOL;
                } else {
                    Yii::error($cs->errors, 'm210930_134439_add_new_virtual_cron_command:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm210930_134439_add_new_virtual_cron_command:safeDown:Throwable');
        }
    }
}
