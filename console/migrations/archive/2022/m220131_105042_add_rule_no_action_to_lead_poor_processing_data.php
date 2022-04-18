<?php

use src\model\leadPoorProcessingData\entity\LeadPoorProcessingData;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use yii\db\Migration;

/**
 * Class m220131_105042_add_rule_no_action_to_lead_poor_processing_data
 */
class m220131_105042_add_rule_no_action_to_lead_poor_processing_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!LeadPoorProcessingData::findOne(['lppd_key' => LeadPoorProcessingDataDictionary::KEY_NO_ACTION])) {
            $this->insert(
                '{{%lead_poor_processing_data}}',
                [
                    'lppd_enabled' => false,
                    'lppd_key' => LeadPoorProcessingDataDictionary::KEY_NO_ACTION,
                    'lppd_name' => 'Last action',
                    'lppd_description' => 'No action within 24 hours',
                    'lppd_minute' => 1440,
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
            LeadPoorProcessingDataDictionary::KEY_NO_ACTION,
        ]]);
    }
}
