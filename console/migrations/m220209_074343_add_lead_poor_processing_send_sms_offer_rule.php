<?php

use src\model\leadPoorProcessingData\entity\LeadPoorProcessingData;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use yii\db\Migration;

/**
 * Class m220209_074343_add_lead_poor_processing_send_sms_offer_rule
 */
class m220209_074343_add_lead_poor_processing_send_sms_offer_rule extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!LeadPoorProcessingData::findOne(['lppd_key' => LeadPoorProcessingDataDictionary::KEY_SEND_SMS_OFFER])) {
            $this->insert(
                '{{%lead_poor_processing_data}}',
                [
                    'lppd_enabled' => false,
                    'lppd_key' => LeadPoorProcessingDataDictionary::KEY_SEND_SMS_OFFER,
                    'lppd_name' => LeadPoorProcessingDataDictionary::KEY_LIST[LeadPoorProcessingDataDictionary::KEY_SEND_SMS_OFFER],
                    'lppd_description' => 'Send SMS Offer, no action within 120 min',
                    'lppd_minute' => 120,
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
            LeadPoorProcessingDataDictionary::KEY_SEND_SMS_OFFER,
        ]]);
    }
}
