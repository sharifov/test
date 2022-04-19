<?php

use yii\db\Migration;

/**
 * Class m211207_094328_add_new_site_setting_user_picked_call_duration
 */
class m211207_094328_add_new_site_setting_user_picked_call_duration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = \common\models\SettingCategory::getOrCreateByName('WebEngage');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'user_picked_call_duration',
                's_name' => 'WebEngage UserPickedCall call duration',
                's_type' => \common\models\Setting::TYPE_INT,
                's_value' => 30,
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
            'user_picked_call_duration',
        ]]);
    }
}
