<?php

use yii\db\Migration;

/**
 * Class m210118_103628_add_UpdateOnlineStatus_virtual_CRON
 */
class m210118_103628_add_UpdateOnlineStatus_virtual_CRON extends Migration
{
    /**
     * @return bool|void
     */
    public function safeUp()
    {
        try {
            $cs = new \kivork\VirtualCron\Models\CronScheduler();
            $cs->cs_cron_expression = '* * * * *';
            $cs->cs_cron_command = 'user/update-online-status';
            $cs->cs_description = 'Update online User status';
            $cs->cs_hash = \kivork\VirtualCron\Services\Hasher::generate(
                $cs->cs_cron_expression,
                $cs->cs_cron_command,
                $cs->cs_cron_params
            );
            $cs->cs_enabled = true;
            if ($cs->save()) {
                echo ' - Hash: ' . $cs->cs_hash;
            } else {
                Yii::error($cs->errors, 'm210118_103628_add_UpdateOnlineStatus_virtual_CRON:safeUp');
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm210118_103628_add_UpdateOnlineStatus_virtual_CRON:safeUp:Throwable');
        }
    }

    /**
     * @return bool|void
     */
    public function safeDown()
    {
        try {
            $cs = \kivork\VirtualCron\Models\CronScheduler::find()->where(['cs_cron_command' => 'user/update-online-status'])->one();
            if ($cs) {
                if ($cs->delete()) {
                    echo ' - Delete: OK' . PHP_EOL;
                } else {
                    Yii::error($cs->errors, 'm210118_103628_add_UpdateOnlineStatus_virtual_CRON:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm210118_103628_add_UpdateOnlineStatus_virtual_CRON:safeDown:Throwable');
        }
    }
}
