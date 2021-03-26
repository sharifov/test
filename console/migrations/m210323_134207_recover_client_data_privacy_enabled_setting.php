<?php

use yii\db\Migration;
use common\models\Setting;
use common\models\SettingCategory;

/**
 * Class m210323_134207_recover_client_data_privacy_enabled_setting
 */
class m210323_134207_recover_client_data_privacy_enabled_setting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $paramExistence = Setting::findOne(['s_key' => 'client_data_privacy_enabled']);
        if (!$paramExistence) {
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
