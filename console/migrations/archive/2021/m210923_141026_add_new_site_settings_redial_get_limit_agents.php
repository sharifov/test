<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m210923_141026_add_new_site_settings_redial_get_limit_agents
 */
class m210923_141026_add_new_site_settings_redial_get_limit_agents extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = \common\models\SettingCategory::getOrCreateByName('Call');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'redial_get_limit_agents',
                's_name' => 'Limit of selected agents for set access to lead redial',
                's_type' => Setting::TYPE_INT,
                's_value' => 5,
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
            'redial_get_limit_agents',
        ]]);
    }
}
