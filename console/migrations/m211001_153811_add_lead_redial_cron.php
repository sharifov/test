<?php

use yii\db\Migration;

/**
 * Class m211001_153811_add_lead_redial_cron
 */
class m211001_153811_add_lead_redial_cron extends Migration
{
    /**
     * @return bool|void
     */
    public function safeUp()
    {
        try {
            $cs = new \kivork\VirtualCron\Models\CronScheduler();
            $cs->cs_cron_expression = '*/1 * * * *';
            $cs->cs_cron_command = 'call/redial-call';
            $cs->cs_description = 'Lead redial assign users';
            $cs->cs_hash = \kivork\VirtualCron\Services\Hasher::generate(
                $cs->cs_cron_expression,
                $cs->cs_cron_command,
                $cs->cs_cron_params
            );
            $cs->cs_enabled = false;
            if ($cs->save()) {
                echo ' - Hash: ' . $cs->cs_hash;
            } else {
                Yii::error($cs->errors, 'm211001_153811_add_lead_redial_cron:safeUp');
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm211001_153811_add_lead_redial_cron:safeUp:Throwable');
        }
    }

    /**
     * @return bool|void
     */
    public function safeDown()
    {
        try {
            $cs = \kivork\VirtualCron\Models\CronScheduler::find()->where(['cs_cron_command' => 'call/redial-call'])->one();
            if ($cs) {
                if ($cs->delete()) {
                    echo ' - Delete: OK' . PHP_EOL;
                } else {
                    Yii::error($cs->errors, 'm211001_153811_add_lead_redial_cron:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm211001_153811_add_lead_redial_cron:safeDown:Throwable');
        }
    }
}
