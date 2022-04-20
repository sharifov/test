<?php

use yii\db\Migration;

/**
 * Class m211001_075137_add_new_site_setting
 */
class m211001_075137_add_new_site_setting extends Migration
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
                's_key' => 'calculate_gross_profit_in_days',
                's_name' => 'Calculate gross profit in days',
                's_type' => \common\models\Setting::TYPE_INT,
                's_value' => 60,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'Option used in search query of available agents for leads redial'
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'calculate_gross_profit_in_days',
        ]]);
    }
}
