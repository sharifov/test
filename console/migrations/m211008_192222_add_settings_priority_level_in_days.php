<?php

use yii\db\Migration;

/**
 * Class m211008_192222_add_settings_priority_level_in_days
 */
class m211008_192222_add_settings_priority_level_in_days extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = \common\models\SettingCategory::getOrCreateByName('User');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'calculate_priority_level_in_days',
                's_name' => 'Calculate user priority level in days',
                's_type' => \common\models\Setting::TYPE_INT,
                's_value' => 14,
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
            'calculate_priority_level_in_days',
        ]]);
    }
}
