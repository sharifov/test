<?php

use src\model\leadPoorProcessingData\entity\LeadPoorProcessingData;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use yii\db\Migration;

/**
 * Class m220202_055620_add_rule_extra_processing_to_lead_poor_processing_data
 */
class m220202_055620_add_rule_extra_processing_to_lead_poor_processing_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!LeadPoorProcessingData::findOne(['lppd_key' => LeadPoorProcessingDataDictionary::KEY_EXTRA_TO_PROCESSING_TAKE])) {
            $this->insert(
                '{{%lead_poor_processing_data}}',
                [
                    'lppd_enabled' => false,
                    'lppd_key' => LeadPoorProcessingDataDictionary::KEY_EXTRA_TO_PROCESSING_TAKE,
                    'lppd_name' => LeadPoorProcessingDataDictionary::KEY_LIST[LeadPoorProcessingDataDictionary::KEY_EXTRA_TO_PROCESSING_TAKE],
                    'lppd_description' => 'Extra to Processing Take, no action within 30 min',
                    'lppd_minute' => 30,
                ]
            );
        }
        if (!LeadPoorProcessingData::findOne(['lppd_key' => LeadPoorProcessingDataDictionary::KEY_EXTRA_TO_PROCESSING_MULTIPLE_UPD])) {
            $this->insert(
                '{{%lead_poor_processing_data}}',
                [
                    'lppd_enabled' => false,
                    'lppd_key' => LeadPoorProcessingDataDictionary::KEY_EXTRA_TO_PROCESSING_MULTIPLE_UPD,
                    'lppd_name' => LeadPoorProcessingDataDictionary::KEY_LIST[LeadPoorProcessingDataDictionary::KEY_EXTRA_TO_PROCESSING_MULTIPLE_UPD],
                    'lppd_description' => 'Extra to Processing Multiple Update, no action within 15 min',
                    'lppd_minute' => 15,
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
            LeadPoorProcessingDataDictionary::KEY_EXTRA_TO_PROCESSING_TAKE,
            LeadPoorProcessingDataDictionary::KEY_EXTRA_TO_PROCESSING_MULTIPLE_UPD,
        ]]);
    }
}
