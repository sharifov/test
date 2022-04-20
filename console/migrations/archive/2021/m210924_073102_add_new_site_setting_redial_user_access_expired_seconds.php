<?php

use yii\db\Migration;

/**
 * Class m210924_073102_add_new_site_setting_redial_user_access_expired_seconds
 */
class m210924_073102_add_new_site_setting_redial_user_access_expired_seconds extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = \common\models\SettingCategory::getOrCreateByName('Redial');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'redial_user_access_expired_seconds',
                's_name' => 'Limit user access to redial leads in seconds',
                's_type' => \common\models\Setting::TYPE_INT,
                's_value' => 60,
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
            'redial_user_access_expired_seconds',
        ]]);
    }
}
