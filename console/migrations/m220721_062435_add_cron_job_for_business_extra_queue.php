<?php

use yii\db\Migration;

/**
 * Class m220721_062435_add_cron_job_for_business_extra_queue
 */
class m220721_062435_add_cron_job_for_business_extra_queue extends Migration
{
    /**
     * @return void
     */
    public function safeUp()
    {
        try {
            $cs = new \kivork\VirtualCron\Models\CronScheduler();
            $cs->cs_cron_expression = '10 * * * *';
            $cs->cs_cron_command = 'lead/to-business-extra-queue';
            $cs->cs_description = 'Cron job to Business Extra Queue';
            $cs->cs_hash = \kivork\VirtualCron\Services\Hasher::generate(
                $cs->cs_cron_expression,
                $cs->cs_cron_command,
                $cs->cs_cron_params
            );
            $cs->cs_enabled = false;
            if ($cs->save()) {
                echo ' - Hash: ' . $cs->cs_hash;
            } else {
                Yii::error($cs->errors, 'm220721_062435_add_cron_job_for_business_extra_queue:safeUp');
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm220721_062435_add_cron_job_for_business_extra_queue:safeUp:Throwable');
        }
    }

    /**
     * @return void
     */
    public function safeDown()
    {
        try {
            $cs = \kivork\VirtualCron\Models\CronScheduler::find()->
            where(['cs_cron_command' => 'lead/to-business-extra-queue'])->one();
            if ($cs) {
                if ($cs->delete()) {
                    echo ' - Delete: OK' . PHP_EOL;
                } else {
                    Yii::error($cs->errors, 'm220721_062435_add_cron_job_for_business_extra_queue:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm220721_062435_add_cron_job_for_business_extra_queue:safeDown:Throwable');
        }
    }
}
