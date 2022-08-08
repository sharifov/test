<?php

use yii\db\Migration;

/**
 * Class m220805_142807_add_cron_jon_for_smart_lead_distribution
 */
class m220805_142807_add_cron_jon_for_smart_lead_distribution extends Migration
{
    private const COMMAND = 'lead/recalculate-rating-business-leads';

    public function safeUp()
    {
        try {
            $cs = new \kivork\VirtualCron\Models\CronScheduler();
            $cs->cs_cron_expression = '*/1 * * * *';
            $cs->cs_cron_command = self::COMMAND;
            $cs->cs_description = 'Recalculate rating for business leads';
            $cs->cs_hash = \kivork\VirtualCron\Services\Hasher::generate(
                $cs->cs_cron_expression,
                $cs->cs_cron_command,
                $cs->cs_cron_params
            );
            $cs->cs_enabled = true;
            if ($cs->save()) {
                echo ' - Hash: ' . $cs->cs_hash;
            } else {
                Yii::error($cs->errors, 'm220805_142807_add_cron_jon_for_smart_lead_distribution:safeUp');
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm220805_142807_add_cron_jon_for_smart_lead_distribution:safeUp:Throwable');
        }
    }

    public function safeDown()
    {
        try {
            $cs = \kivork\VirtualCron\Models\CronScheduler::find()->where(['cs_cron_command' => self::COMMAND])->one();
            if ($cs) {
                if ($cs->delete()) {
                    echo ' - Delete: OK' . PHP_EOL;
                } else {
                    Yii::error($cs->errors, 'm220805_142807_add_cron_jon_for_smart_lead_distribution:safeDown');
                }
            }
        } catch (Throwable $throwable) {
            Yii::error($throwable, 'm220805_142807_add_cron_jon_for_smart_lead_distribution:safeDown:Throwable');
        }
    }
}
