<?php

use src\model\leadDataKey\services\LeadDataKeyDictionary;
use yii\db\Migration;

/**
 * Class m220810_051736_rename_lead_data_keys_for_smart_lead_distribution
 */
class m220810_051736_rename_lead_data_keys_for_smart_lead_distribution extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update('{{%lead_data_key}}', [
            'ldk_name' => 'Lead Rating points - fixed',
        ], [
            'ldk_key' => LeadDataKeyDictionary::KEY_LEAD_RATING_POINTS
        ]);

        $this->update('{{%lead_data_key}}', [
            'ldk_name' => 'Lead Rating category - fixed',
        ], [
            'ldk_key' => LeadDataKeyDictionary::KEY_LEAD_RATING_CATEGORY
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->update('{{%lead_data_key}}', [
            'ldk_name' => 'Lead Rating points',
        ], [
            'ldk_key' => LeadDataKeyDictionary::KEY_LEAD_RATING_POINTS
        ]);

        $this->update('{{%lead_data_key}}', [
            'ldk_name' => 'Lead Rating category',
        ], [
            'ldk_key' => LeadDataKeyDictionary::KEY_LEAD_RATING_CATEGORY
        ]);
    }
}
