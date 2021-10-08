<?php

use yii\db\Migration;

/**
 * Class m211008_192804_add_virtual_cron_command_calculate_priority_level
 */
class m211008_192804_add_virtual_cron_command_calculate_priority_level extends Migration
{
    public function safeUp()
    {
        try {
            $cs = new \kivork\VirtualCron\Models\CronScheduler();
            $cs->cs_cron_expression = '5 0 * * *';
            $cs->cs_cron_command = 'user/calculate-priority-level';
            $cs->cs_description = 'Calculate users priority level';
            $cs->cs_hash = \kivork\VirtualCron\Services\Hasher::generate(
                $cs->cs_cron_expression,
                $cs->cs_cron_command,
                $cs->cs_cron_params
            );
            $cs->cs_enabled = false;
            if ($cs->save()) {
                echo ' - Hash: ' . $cs->cs_hash;
            } else {
                Yii::error($cs->errors, 'm211008_192804_add_virtual_cron_command_calculate_priority_level:safeUp');
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm211008_192804_add_virtual_cron_command_calculate_priority_level:safeUp:Throwable');
        }
    }

    public function safeDown()
    {
        try {
            $cs = \kivork\VirtualCron\Models\CronScheduler::find()->where(['cs_cron_command' => 'user/calculate-priority-level'])->one();
            if ($cs) {
                if ($cs->delete()) {
                    echo ' - Delete: OK' . PHP_EOL;
                } else {
                    Yii::error($cs->errors, 'm211008_192804_add_virtual_cron_command_calculate_priority_level:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm211008_192804_add_virtual_cron_command_calculate_priority_level:safeDown:Throwable');
        }
    }
}
