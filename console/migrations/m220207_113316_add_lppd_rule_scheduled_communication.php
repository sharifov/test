<?php

use src\model\leadPoorProcessingData\entity\LeadPoorProcessingData;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use yii\db\Migration;

/**
 * Class m220207_113316_add_lppd_rule_scheduled_communication
 */
class m220207_113316_add_lppd_rule_scheduled_communication extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!LeadPoorProcessingData::findOne(['lppd_key' => LeadPoorProcessingDataDictionary::KEY_SCHEDULED_COMMUNICATION])) {
            $this->insert(
                '{{%lead_poor_processing_data}}',
                [
                    'lppd_enabled' => false,
                    'lppd_key' => LeadPoorProcessingDataDictionary::KEY_SCHEDULED_COMMUNICATION,
                    'lppd_name' => 'Scheduled communication',
                    'lppd_description' => 'Not scheduled communication within 24 hours',
                    'lppd_minute' => 1,
                    'lppd_params_json' => [
                        'intervalHour' => 24,
                        'callOut' => 2,
                        'smsOut' => 1,
                        'emailOffer' => 1,
                    ],
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
            LeadPoorProcessingDataDictionary::KEY_SCHEDULED_COMMUNICATION,
        ]]);
    }
}
