<?php

use yii\db\Migration;

/**
 * Class m220805_075117_add_check_user_activity_virtual_CRON
 */
class m220805_075117_add_check_user_activity_virtual_CRON extends Migration
{
    /**
     * @return void
     */
    public function safeUp()
    {
        try {
            $cs = new \kivork\VirtualCron\Models\CronScheduler();
            $cs->cs_cron_expression = '* * * * *';
            $cs->cs_cron_command = 'user/check-activity';
            $cs->cs_description = 'Check User Activity events';
            $cs->cs_hash = \kivork\VirtualCron\Services\Hasher::generate(
                $cs->cs_cron_expression,
                $cs->cs_cron_command,
                $cs->cs_cron_params
            );
            $cs->cs_enabled = false;
            if ($cs->save()) {
                echo ' - Hash: ' . $cs->cs_hash;
            } else {
                Yii::error($cs->errors, 'm220805_075117_add_check_user_activity_virtual_CRON:safeUp');
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm220805_075117_add_check_user_activity_virtual_CRON:safeUp:Throwable');
        }
    }

    /**
     * @return void
     */
    public function safeDown()
    {
        try {
            $cs = \kivork\VirtualCron\Models\CronScheduler::find()->
            where(['cs_cron_command' => 'user/check-activity'])->one();
            if ($cs) {
                if ($cs->delete()) {
                    echo ' - Delete: OK' . PHP_EOL;
                } else {
                    Yii::error($cs->errors, 'm220805_075117_add_check_user_activity_virtual_CRON:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm220805_075117_add_check_user_activity_virtual_CRON:safeDown:Throwable');
        }
    }
}
