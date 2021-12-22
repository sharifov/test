<?php

use yii\db\Migration;

/**
 * Class m211222_120814_add_new_script_in_virtual_cron
 */
class m211222_120814_add_new_script_in_virtual_cron extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $cs = new \kivork\VirtualCron\Models\CronScheduler();
            $cs->cs_cron_expression = '0 0 1 1 *';
            $cs->cs_cron_command = 'lead/update-hybrid-uid';
            $cs->cs_description = 'Update lead hybrid uid parameter; Need to pass the date as parameters in format Y-m-d H:i:s';
            $cs->cs_hash = \kivork\VirtualCron\Services\Hasher::generate(
                $cs->cs_cron_expression,
                $cs->cs_cron_command,
                $cs->cs_cron_params
            );
            $cs->cs_enabled = false;
            if ($cs->save()) {
                echo ' - Hash: ' . $cs->cs_hash;
            } else {
                Yii::error($cs->errors, 'm211222_120814_add_new_script_in_virtual_cron:safeUp');
            }
        } catch (Throwable $throwable) {
            \Yii::error(
                \sales\helpers\app\AppHelper::throwableLog($throwable),
                'm211222_120814_add_new_script_in_virtual_cron:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%cron_scheduler}}', ['IN', 'cs_cron_command', [
            'lead/update-hybrid-uid',
        ]]);
    }
}
