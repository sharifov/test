<?php

use yii\db\Migration;

/**
 * Class m211018_081710_OwnerToProfitSplitAction_virtual_CRON
 */
class m211018_081710_OwnerToProfitSplitAction_virtual_CRON extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $cs = new \kivork\VirtualCron\Models\CronScheduler();
            $cs->cs_cron_expression = '0 2 * * *';
            $cs->cs_cron_command = 'profit-split/owner-to-profit-split';
            $cs->cs_description = 'Owner to Profit Split';
            $cs->cs_hash = \kivork\VirtualCron\Services\Hasher::generate(
                $cs->cs_cron_expression,
                $cs->cs_cron_command,
                $cs->cs_cron_params
            );
            $cs->cs_enabled = false;

            if ($cs->save()) {
                echo ' - Hash: ' . $cs->cs_hash;
            } else {
                Yii::error($cs->errors, 'm211018_081710_OwnerToProfitSplitAction_virtual_CRON:safeUp');
            }
        } catch (Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm211018_081710_OwnerToProfitSplitAction_virtual_CRON:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $cs = \kivork\VirtualCron\Models\CronScheduler::find()->where(['cs_cron_command' => 'case/case-to-need'])->one();
            if ($cs) {
                if ($cs->delete()) {
                    echo ' - Delete: OK' . PHP_EOL;
                } else {
                    Yii::error($cs->errors, 'm211018_081710_OwnerToProfitSplitAction_virtual_CRON:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm211018_081710_OwnerToProfitSplitAction_virtual_CRON:safeDown:Throwable'
            );
        }
    }
}
