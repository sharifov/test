<?php

use common\models\Setting;
use common\models\SettingCategory;
use frontend\helpers\JsonHelper;
use yii\db\Migration;

/**
 * Class m210926_171512_add_new_site_settings_callback_to_caller
 */
class m210926_171512_add_new_site_settings_callback_to_caller extends Migration
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
                's_key' => 'callback_to_caller',
                's_name' => 'Callback to caller',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => JsonHelper::encode([
                    'enabled' => false,
                    'message' => '',
                    'curlTimeout' => 30,
                    'dialCallTimeout' => 10,
                    'dialCallLimit' => 1,
                    'successStatusList' => [
                        'busy'
                    ]
                ]),
                's_description' => 'This param is depends by call_spam_filter; Allowed status list: busy, in-progress, complete, failed, no-answered',
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
            'callback_to_caller',
        ]]);
    }
}
