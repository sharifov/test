<?php

use yii\db\Migration;
use common\models\Setting;
use common\models\SettingCategory;

/**
 * Class m210210_051457_add_site_setting_client_data_privacy_enabled
 */
class m210210_051457_add_site_setting_client_data_privacy_enabled extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Client data privacy');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'client_data_privacy_enabled',
                's_name' => 'Client data privacy enabled',
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
            'client_data_privacy_enabled',
        ]]);
    }
}
