<?php

use common\models\Setting;
use common\models\SettingCategory;
use frontend\helpers\JsonHelper;
use yii\db\Migration;

/**
 * Class m211006_115611_add_redial_priority_level_settings
 */
class m211006_115611_add_redial_priority_level_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Redial');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'redial_priority_level',
                's_name' => 'Redial priority level',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => JsonHelper::encode([
                    '0' => '5',
                    '1' => '10',
                    '2' => '20',
                    '3' => '30',
                    '4' => '40',
                    '5' => '50',
                    'default' => -1,
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
            'redial_priority_level',
        ]]);
    }
}
