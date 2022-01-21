<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m210219_150336_add_new_site_setting_for_flight_quote_auto_select
 */
class m210219_150336_add_new_site_setting_for_flight_quote_auto_select extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('General');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'flight_quote_auto_select_count',
                's_name' => 'Flight Quote auto-select count',
                's_type' => Setting::TYPE_INT,
                's_value' => 3,
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
            'flight_quote_auto_select_count'
        ]]);
    }
}
