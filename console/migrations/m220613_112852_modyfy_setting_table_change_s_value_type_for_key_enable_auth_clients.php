<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m220613_112852_modyfy_setting_table_change_s_value_type_for_key_enable_auth_clients
 */
class m220613_112852_modyfy_setting_table_change_s_value_type_for_key_enable_auth_clients extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $setting = Setting::findOne(['s_key' => 'enable_auth_clients']);
        if ($setting) {
            $setting->s_name = 'Enable authorization via social media clients (Google/Microsoft)';
            $setting->s_type = Setting::TYPE_ARRAY;
            $setting->s_value = json_encode([
                'auth_google' => false,
                'auth_microsoft' => false,
            ]);
            $setting->s_updated_dt = date('Y-m-d H:i:s');
            $setting->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220613_112852_modyfy_setting_table_change_s_value_type_for_key_enable_auth_clients cannot be reverted.\n";
    }
}
