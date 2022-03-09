<?php

use yii\db\Migration;

/**
 * Class m220309_122504_add_automate_sync_currency_virtual_cron
 */
class m220309_122504_add_automate_sync_currency_virtual_cron extends Migration
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
                    'service/update-currency',
                ]
            ]);
            $cs                     = new \kivork\VirtualCron\Models\CronScheduler();
            $cs->cs_cron_expression = '1 * * * *';
            $cs->cs_cron_command    = 'service/update-currency';
            $cs->cs_description     = 'Update Currency Rates from Currency Service';
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
                'm220309_122504_add_automate_sync_currency_virtual_cron:safeUp:Throwable'
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
                    'service/update-currency',
                ]
            ]);
        } catch (Throwable $throwable) {
            Yii::error(
                $throwable,
                'm220309_122504_add_automate_sync_currency_virtual_cron:safeDown:Throwable'
            );
        }
    }
}
