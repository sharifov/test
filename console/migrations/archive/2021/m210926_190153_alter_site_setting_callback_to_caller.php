<?php

use yii\db\Migration;

/**
 * Class m210926_190153_alter_site_setting_callback_to_caller
 */
class m210926_190153_alter_site_setting_callback_to_caller extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'callback_to_caller',
        ]]);

        $settingCategory = \common\models\SettingCategory::getOrCreateByName('Call');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'callback_to_caller',
                's_name' => 'Callback to caller',
                's_type' => \common\models\Setting::TYPE_ARRAY,
                's_value' => \frontend\helpers\JsonHelper::encode([
                    'enabled' => false,
                    'message' => '',
                    'curlTimeout' => 30,
                    'dialCallTimeout' => 10,
                    'dialCallLimit' => 1,
                    'successStatusList' => [
                        'busy'
                    ],
                    'excludeProjectKeys' => [
                        'priceline'
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
    }
}
