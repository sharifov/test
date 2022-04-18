<?php

use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m211004_073433_autoTrashFollowUpLeads
 */
class m211004_073433_autoTrashFollowUpLeads_virtual_CRON extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $cs = new \kivork\VirtualCron\Models\CronScheduler();
            $cs->cs_cron_expression = '0 7 * * *';
            $cs->cs_cron_command = 'lead/lead-to-trash';
            $cs->cs_description = 'Auto Trash Follow Up leads with dates passed';
            $cs->cs_hash = \kivork\VirtualCron\Services\Hasher::generate(
                $cs->cs_cron_expression,
                $cs->cs_cron_command,
                $cs->cs_cron_params
            );
            $cs->cs_enabled = false;
            if ($cs->save()) {
                echo ' - Hash: ' . $cs->cs_hash;
            } else {
                Yii::error($cs->errors, 'm211004_073433_autoTrashFollowUpLeads_virtual_CRON:safeUp');
            }
        } catch (Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm211004_073433_autoTrashFollowUpLeads_virtual_CRON:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $cs = \kivork\VirtualCron\Models\CronScheduler::find()->where(['cs_cron_command' => 'lead/lead-to-trash'])->one();
            if ($cs) {
                if ($cs->delete()) {
                    echo ' - Delete: OK' . PHP_EOL;
                } else {
                    Yii::error($cs->errors, 'm211004_073433_autoTrashFollowUpLeads_virtual_CRON:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm211004_073433_autoTrashFollowUpLeads_virtual_CRON:safeDown:Throwable'
            );
        }
    }
}
