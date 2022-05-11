<?php

use yii\db\Migration;

/**
 * Class m220418_085003_add_virtual_cron_job
 */
class m220418_085003_add_virtual_cron_job extends Migration
{
    /**
     * @return void
     */
    public function safeUp()
    {
        try {
            $cs = new \kivork\VirtualCron\Models\CronScheduler();
            $cs->cs_cron_expression = '30 1 * * *';
            $cs->cs_cron_command = 'shift/generate-user-schedule';
            $cs->cs_description = 'User shift schedule generator console script';
            $cs->cs_hash = \kivork\VirtualCron\Services\Hasher::generate(
                $cs->cs_cron_expression,
                $cs->cs_cron_command,
                $cs->cs_cron_params
            );
            $cs->cs_enabled = true;
            if ($cs->save()) {
                echo ' - Hash: ' . $cs->cs_hash;
            } else {
                Yii::error($cs->errors, 'm220418_085003_add_virtual_cron_job:safeUp');
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm220418_085003_add_virtual_cron_job:safeUp:Throwable');
        }
    }

    /**
     * @return void
     */
    public function safeDown()
    {
        try {
            $cs = \kivork\VirtualCron\Models\CronScheduler::find()->
            where(['cs_cron_command' => 'shift/generate-user-schedule'])->one();
            if ($cs) {
                if ($cs->delete()) {
                    echo ' - Delete: OK' . PHP_EOL;
                } else {
                    Yii::error($cs->errors, 'm220418_085003_add_virtual_cron_job:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm220418_085003_add_virtual_cron_job:safeDown:Throwable');
        }
    }
}
