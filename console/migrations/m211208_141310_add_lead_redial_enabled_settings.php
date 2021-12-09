<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m211208_141310_add_lead_redial_enabled_settings
 */
class m211208_141310_add_lead_redial_enabled_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Redial');
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'lead_redial_enabled',
                's_name' => 'Lead redial enabled',
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
            'lead_redial_enabled',
        ]]);
    }
}
