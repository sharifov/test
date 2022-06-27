<?php

use src\model\leadDataKey\entity\LeadDataKey;
use src\model\leadDataKey\services\LeadDataKeyDictionary;
use yii\db\Migration;

/**
 * Class m220627_084449_correction_lead_data_key_lead_object_segment
 */
class m220627_084449_correction_lead_data_key_lead_object_segment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (LeadDataKey::find()->where(['ldk_key' => LeadDataKeyDictionary::KEY_LEAD_OBJECT_SEGMENT])->exists()) {
            LeadDataKey::updateAll(
                ['ldk_is_system' => true],
                ['ldk_key' => LeadDataKeyDictionary::KEY_LEAD_OBJECT_SEGMENT]
            );
        } else {
            Yii::$app->db->createCommand()->upsert('{{%lead_data_key}}', [
                'ldk_key' => LeadDataKeyDictionary::KEY_LEAD_OBJECT_SEGMENT,
                'ldk_name' => 'Lead Object Segment',
                'ldk_enable' => true,
                'ldk_is_system' => true,
                'ldk_created_dt' => date('Y-m-d H:i:s')
            ])->execute();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220627_084449_correction_lead_data_key_lead_object_segment cannot be reverted.\n";
    }
}
