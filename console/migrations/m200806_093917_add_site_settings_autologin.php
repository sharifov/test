<?php

use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m200806_093917_add_site_settings_autologin
 */
class m200806_093917_add_site_settings_autologin extends Migration
{

    public function safeUp()
    {

        $settingCategory = new SettingCategory();
        $settingCategory->sc_name = 'AutoLogOut';
        if (!$settingCategory->save()) {
            print_r($settingCategory->errors);
            return false;
        }

        $this->insert('{{%setting}}', [
            's_key' => 'autologout_enabled',
            's_name' => 'Auto LogOut enabled',
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => 0,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $settingCategory->sc_id
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'autologout_timer_sec',
            's_name' => 'Auto LogOut timer seconds',
            's_type' => \common\models\Setting::TYPE_INT,
            's_value' => 60,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $settingCategory->sc_id
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'autologout_show_message',
            's_name' => 'Auto LogOut show message',
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => 1,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $settingCategory->sc_id
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'autologout_idle_period_min',
            's_name' => 'Auto LogOut idle period minutes',
            's_type' => \common\models\Setting::TYPE_INT,
            's_value' => 15,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $settingCategory->sc_id
        ]);

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
            'autologout_enabled', 'autologout_timer_sec','autologout_show_message', 'autologout_idle_period_min'
        ]]);


        $this->delete('{{%setting_category}}', ['IN', 'sc_name', [
            'AutoLogOut'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
