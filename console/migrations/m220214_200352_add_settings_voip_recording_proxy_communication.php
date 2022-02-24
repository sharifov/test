<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m220214_200352_add_settings_voip_recording_proxy_communication
 */
class m220214_200352_add_settings_voip_recording_proxy_communication extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Call');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'call_enabled_communication_proxy_recordings',
                's_name' => 'Enable communication proxy recording',
                's_type' => Setting::TYPE_BOOL,
                's_value' => false,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'Enable communication proxy recording',
            ]
        );

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'call_enabled_communication_proxy_recordings'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
