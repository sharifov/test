<?php

use yii\db\Migration;

/**
 * Class m220727_065605_add_cron_command_from_beq_to_closed_fix
 */
class m220727_065605_add_cron_command_from_beq_to_closed_fix extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $this->delete('{{%cron_scheduler}}', [
                'IN',
                'cs_cron_command',
                [
                    'lead/to-closed-from-business-extra',
                ]
            ]);
            $cs                     = new \kivork\VirtualCron\Models\CronScheduler();
            $cs->cs_cron_expression = '30 8 * * *';
            $cs->cs_cron_command    = 'lead/to-closed-from-business-extra';
            $cs->cs_description     = 'Leads from Business Extra Queue To Closed due expiration';
            $cs->cs_hash            = \kivork\VirtualCron\Services\Hasher::generate(
                $cs->cs_cron_expression,
                $cs->cs_cron_command,
                $cs->cs_cron_params
            );
            $cs->cs_enabled         = false;
            if (!$cs->save()) {
                echo $cs->getErrorSummary(true)[0];
            }
        } catch (Throwable $throwable) {
            Yii::error(
                $throwable,
                'm220727_065605_add_cron_command_from_beq_to_closed_fix:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $this->delete('{{%cron_scheduler}}', [
                'IN',
                'cs_cron_command',
                [
                    'lead/to-closed-from-business-extra',
                ]
            ]);
        } catch (Throwable $throwable) {
            Yii::error(
                $throwable,
                'm220727_065605_add_cron_command_from_beq_to_closed_fix:safeDown:Throwable'
            );
        }
    }
}
