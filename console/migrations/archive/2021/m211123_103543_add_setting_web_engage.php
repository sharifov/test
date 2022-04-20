<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m211123_103543_add_setting_webengage
 */
class m211123_103543_add_setting_web_engage extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('External service');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'web_engage',
                's_name' => 'WebEngage settings',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => \frontend\helpers\JsonHelper::encode([
                    'enable' => false,
                    'license_code' => '',
                    'api_key' => '',
                    'tracking_events_host' => '',
                    'debug_enable' => false,
                    'is_test' => false,
                ]),
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'Settings for WebEngage Service',
            ]
        );

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'web_engage',
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
