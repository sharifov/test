<?php

use src\model\leadPoorProcessingData\entity\LeadPoorProcessingData;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use yii\db\Migration;

/**
 * Class m220126_122629_add_rule_last_action_to_lead_poor_processing_data
 */
class m220126_122629_add_rule_last_action_to_lead_poor_processing_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!LeadPoorProcessingData::findOne(['lppd_key' => LeadPoorProcessingDataDictionary::KEY_LAST_ACTION])) {
            $this->insert(
                '{{%lead_poor_processing_data}}',
                [
                    'lppd_enabled' => false,
                    'lppd_key' => LeadPoorProcessingDataDictionary::KEY_LAST_ACTION,
                    'lppd_name' => 'Last action',
                    'lppd_description' => 'Last action older than 72 hours ago',
                    'lppd_minute' => 4320,
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
            LeadPoorProcessingDataDictionary::KEY_LAST_ACTION,
        ]]);
    }
}
