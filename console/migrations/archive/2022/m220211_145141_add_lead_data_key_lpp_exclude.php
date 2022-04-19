<?php

use src\model\leadDataKey\services\LeadDataKeyDictionary;
use yii\db\Migration;

/**
 * Class m220211_145141_add_lead_data_key_lpp_exclude
 */
class m220211_145141_add_lead_data_key_lpp_exclude extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand()->upsert('{{%lead_data_key}}', [
            'ldk_key' => LeadDataKeyDictionary::KEY_LPP_EXCLUDE,
            'ldk_name' => 'Lead Poor Processing',
            'ldk_enable' => true,
            'ldk_is_system' => true,
            'ldk_created_dt' => date('Y-m-d H:i:s')
        ])->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%lead_data_key}}', ['IN', 'ldk_key', [
            LeadDataKeyDictionary::KEY_LPP_EXCLUDE,
        ]]);
    }
}
