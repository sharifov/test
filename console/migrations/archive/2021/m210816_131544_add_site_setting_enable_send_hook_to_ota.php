<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m210816_131544_add_site_setting_enable_send_hook_to_ota
 */
class m210816_131544_add_site_setting_enable_send_hook_to_ota extends Migration
{
    public string $key = 'enable_send_hook_to_ota_re_protection_create';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Enable');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => $this->key,
                's_name' => 'Enable send hook to OTA ReProtection create',
                's_description' => 'Send hook to OTA in flight reProtection/create API flow',
                's_type' => Setting::TYPE_BOOL,
                's_value' => true,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
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
            $this->key
        ]]);
    }
}
