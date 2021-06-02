<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;
use frontend\helpers\JsonHelper;

/**
 * Class m210602_075614_add_setting_frontend_widget_list
 */
class m210602_075614_add_setting_frontend_widget_list extends Migration
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
                's_key' => 'frontend_widget_list',
                's_name' => 'Frontend widget list',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => JsonHelper::encode([
                    'louassist' => [
                        'enabled' => true,
                        'params' => [
                            'identify' => '97980cfea0067',
                            'company' => 'Example Company',
                            'permissions' => 'admin',
                            'plan' => 'premium',
                        ],
                        'routes' => [
                            '*',
                        ]
                    ]
                ]),
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
            'frontend_widget_list',
        ]]);
    }
}
