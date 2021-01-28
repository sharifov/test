<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m210128_115312_add_general_line_priority_settings
 */
class m210128_115312_add_general_line_priority_settings extends Migration
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
                's_key' => 'enable_general_line_priority',
                's_name' => 'Enable General Line priority',
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
            'enable_general_line_priority',
        ]]);
    }
}
