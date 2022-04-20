<?php

use yii\db\Migration;

/**
 * Class m211009_132053_add_setting_lead_redial_users_sort
 */
class m211009_132053_add_setting_lead_redial_users_sort extends Migration
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
                's_key' => 'lead_redial_sort_users',
                's_name' => 'Lead redial sort users',
                's_type' => \common\models\Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    'priority_level' => 'DESC',
                    'gross_profit' => 'DESC',
                    'phone_ready_time' => 'ASC',
                ]),
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
            'lead_redial_sort_users',
        ]]);
    }
}
