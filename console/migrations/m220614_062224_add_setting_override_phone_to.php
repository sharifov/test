<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m220614_062224_add_setting_override_phone_to
 */
class m220614_062224_add_setting_override_phone_to extends Migration
{
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Call');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'call_sync_override_phone_to_enable',
                's_name' => 'Sync override phone to number to forwarder from number',
                's_type' => Setting::TYPE_BOOL,
                's_value' => false,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->delete('{{%setting}}', ['IN', 's_key', [
            'call_sync_override_phone_to_enable',
        ]]);
    }
}
