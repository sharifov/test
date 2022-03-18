<?php

use yii\db\Migration;

/**
 * Class m220317_095700_add_virtual_cron_lead_from_extra_to_closed_queue
 */
class m220317_095700_add_virtual_cron_lead_from_extra_to_closed_queue extends Migration
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
                    'lead/to-closed-from-extra',
                ]
            ]);
            $cs                     = new \kivork\VirtualCron\Models\CronScheduler();
            $cs->cs_cron_expression = '30 8 * * *';
            $cs->cs_cron_command    = 'lead/to-closed-from-extra';
            $cs->cs_description     = 'Leads from Extra Queue To Closed due expiration';
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
                'm220317_095700_add_virtual_cron_lead_from_extra_to_closed_queue:safeUp:Throwable'
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
                    'lead/to-closed-from-extra',
                ]
            ]);
        } catch (Throwable $throwable) {
            Yii::error(
                $throwable,
                'm220317_095700_add_virtual_cron_lead_from_extra_to_closed_queue:safeDown:Throwable'
            );
        }
    }
}
