<?php

use src\model\leadDataKey\entity\LeadDataKey;
use src\model\leadDataKey\services\LeadDataKeyDictionary;
use yii\caching\TagDependency;
use yii\db\Migration;

/**
 * Class m220727_143723_add_keys_for_lead_rating_to_lead_data_key_table
 */
class m220727_143723_add_keys_for_lead_rating_to_lead_data_key_table extends Migration
{
    public function safeUp()
    {
        $this->insert('{{%lead_data_key}}', [
            'ldk_key' => LeadDataKeyDictionary::KEY_LEAD_RATING_POINTS,
            'ldk_name' => 'Lead Rating points',
            'ldk_enable' => true,
            'ldk_is_system' => true,
            'ldk_created_dt' => date('Y-m-d H:i:s')
        ]);

        $this->insert('{{%lead_data_key}}', [
            'ldk_key' => LeadDataKeyDictionary::KEY_LEAD_RATING_CATEGORY,
            'ldk_name' => 'Lead Rating category',
            'ldk_enable' => true,
            'ldk_is_system' => true,
            'ldk_created_dt' => date('Y-m-d H:i:s')
        ]);

        TagDependency::invalidate(Yii::$app->cache, LeadDataKey::CACHE_TAG);
    }


    public function safeDown()
    {
        $this->delete('{{%lead_data_key}}', ['IN', 'ldk_key', [
            LeadDataKeyDictionary::KEY_LEAD_RATING_POINTS
        ]]);

        $this->delete('{{%lead_data_key}}', ['IN', 'ldk_key', [
            LeadDataKeyDictionary::KEY_LEAD_RATING_CATEGORY
        ]]);

        TagDependency::invalidate(Yii::$app->cache, LeadDataKey::CACHE_TAG);
    }
}
