<?php

use yii\db\Migration;
use src\model\leadBusinessExtraQueueRule\entity\LeadBusinessExtraQueueRule;

/**
 * Class m220726_052228_add_repeated_process_rule_to_lead_business_extra_queue_table
 */
class m220726_052228_add_repeated_process_rule_to_lead_business_extra_queue_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $value = (new \yii\db\Query())->from('{{%lead_business_extra_queue_rules}}')->select(['lbeqr_type_id'])->where(['lbeqr_type_id' => LeadBusinessExtraQueueRule::TYPE_ID_REPEATED_PROCESS_RULE])->limit(1)->one();
            if (!$value) {
                $this->insert('{{%lead_business_extra_queue_rules}}', [
                    'lbeqr_key' => 'repeated_rule',
                    'lbeqr_name' => 'Rule For Repeated Process',
                    'lbeqr_description' => 'Rule For Repeated Process',
                    'lbeqr_params_json' => '{}',
                    'lbeqr_start_time' => '00:00',
                    'lbeqr_end_time' => '23:59',
                    'lbeqr_enabled' => false,
                    'lbeqr_type_id' => LeadBusinessExtraQueueRule::TYPE_ID_REPEATED_PROCESS_RULE,
                    'lbeqr_duration' => 30
                ]);
            }
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220726_052228_add_repeated_process_rule_to_lead_business_extra_queue_table:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $value = (new \yii\db\Query())->from('{{%lead_business_extra_queue_rules}}')->select(['lbeqr_type_id'])->where(['lbeqr_type_id' => LeadBusinessExtraQueueRule::TYPE_ID_REPEATED_PROCESS_RULE])->limit(1)->one();
            if ($value) {
                $this->delete('{{%lead_business_extra_queue_rules}}', ['osr_osl_id' => LeadBusinessExtraQueueRule::TYPE_ID_REPEATED_PROCESS_RULE]);
            }
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220726_052228_add_repeated_process_rule_to_lead_business_extra_queue_table:safeDown:Throwable'
            );
        }
    }
}
