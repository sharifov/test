<?php

use src\model\leadPoorProcessingData\entity\LeadPoorProcessingData;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use yii\db\Migration;

/**
 * Class m220207_114903_add_rule_expert_idle_to_lead_poor_processing_data
 */
class m220207_114903_add_rule_expert_idle_to_lead_poor_processing_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!LeadPoorProcessingData::findOne(['lppd_key' => LeadPoorProcessingDataDictionary::KEY_EXPERT_IDLE])) {
            $this->insert(
                '{{%lead_poor_processing_data}}',
                [
                    'lppd_enabled' => false,
                    'lppd_key' => LeadPoorProcessingDataDictionary::KEY_EXPERT_IDLE,
                    'lppd_name' => LeadPoorProcessingDataDictionary::KEY_LIST[LeadPoorProcessingDataDictionary::KEY_EXPERT_IDLE],
                    'lppd_description' => 'Expert idle, no action within 60 min',
                    'lppd_minute' => 60,
                ]
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%lead_poor_processing_data}}', ['IN', 'lppd_key', [
            LeadPoorProcessingDataDictionary::KEY_EXPERT_IDLE,
        ]]);
    }
}
