<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m211205_142643_add_phone_device_log_setting
 */
class m211205_142643_add_phone_device_log_setting extends Migration
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
                's_key' => 'phone_device_logs_enabled',
                's_name' => 'Phone device logs enabled',
                's_type' => Setting::TYPE_BOOL,
                's_value' => true,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => null,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'phone_device_logs_enabled',
        ]]);
    }
}
