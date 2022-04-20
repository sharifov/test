<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m210916_121049_add_new_site_settings_option
 */
class m210916_121049_add_new_site_settings_option extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Widget');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'limit_leads_in_phone_widget',
                's_name' => 'Client notification start interval',
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
            'limit_leads_in_phone_widget',
        ]]);
    }
}
