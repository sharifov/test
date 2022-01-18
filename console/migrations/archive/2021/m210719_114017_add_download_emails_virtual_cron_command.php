<?php

use yii\db\Migration;

/**
 * Class m210719_114017_add_download_emails_virtual_cron_command
 */
class m210719_114017_add_download_emails_virtual_cron_command extends Migration
{
    public function safeUp()
    {
        try {
            $cs = new \kivork\VirtualCron\Models\CronScheduler();
            $cs->cs_cron_expression = '*/1 * * * *';
            $cs->cs_cron_command = 'email/download';
            $cs->cs_description = 'Download emails';
            $cs->cs_hash = \kivork\VirtualCron\Services\Hasher::generate(
                $cs->cs_cron_expression,
                $cs->cs_cron_command,
                $cs->cs_cron_params
            );
            $cs->cs_enabled = false;
            if ($cs->save()) {
                echo ' - Hash: ' . $cs->cs_hash;
            } else {
                Yii::error($cs->errors, 'm210719_114017_add_download_emails_virtual_cron_command:safeUp');
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm210719_114017_add_download_emails_virtual_cron_command:safeUp:Throwable');
        }
    }

    public function safeDown()
    {
        try {
            $cs = \kivork\VirtualCron\Models\CronScheduler::find()->where(['cs_cron_command' => 'email/download'])->one();
            if ($cs) {
                if ($cs->delete()) {
                    echo ' - Delete: OK' . PHP_EOL;
                } else {
                    Yii::error($cs->errors, 'm210719_114017_add_download_emails_virtual_cron_command:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm210719_114017_add_download_emails_virtual_cron_command:safeDown:Throwable');
        }
    }
}
