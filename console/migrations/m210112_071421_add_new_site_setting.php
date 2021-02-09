<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m210112_071421_add_new_site_setting
 */
class m210112_071421_add_new_site_setting extends Migration
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
                's_key' => 'call_recording_security',
                's_name' => 'Call Recording Security',
                's_type' => Setting::TYPE_BOOL,
                's_value' => false,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'enable_call_recording_log',
                's_name' => 'Enable Call Recording Log',
                's_type' => Setting::TYPE_BOOL,
                's_value' => false,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'call_recording_log_additional_cache_timeout',
                's_name' => 'Additional cache timeout for call recording log (seconds)',
                's_type' => Setting::TYPE_INT,
                's_value' => 60,
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
            'call_recording_security',
            'enable_call_recording_log'
        ]]);
    }
}
