<?php

use src\model\leadDataKey\entity\LeadDataKey;
use src\model\leadDataKey\services\LeadDataKeyDictionary;
use yii\caching\TagDependency;
use yii\db\Migration;

/**
 * Class m220819_074604_add_new_lead_data_key
 */
class m220819_074604_add_new_lead_data_key extends Migration
{
    public function safeUp()
    {
        $this->insert('{{%lead_data_key}}', [
            'ldk_key' => LeadDataKeyDictionary::KEY_AUTO_FOLLOW_UP,
            'ldk_name' => 'Auto Follow Up scenario',
            'ldk_enable' => true,
            'ldk_is_system' => true,
            'ldk_created_dt' => date('Y-m-d H:i:s')
        ]);

        TagDependency::invalidate(Yii::$app->cache, LeadDataKey::CACHE_TAG);
    }


    public function safeDown()
    {
        $this->delete('{{%lead_data_key}}', ['IN', 'ldk_key', [
            LeadDataKeyDictionary::KEY_AUTO_FOLLOW_UP
        ]]);

        TagDependency::invalidate(Yii::$app->cache, LeadDataKey::CACHE_TAG);
    }
}
