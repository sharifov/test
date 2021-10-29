<?php

use yii\db\Migration;

/**
 * Class m211029_100121_add_redial_is_on_call_time_settings
 */
class m211029_100121_add_redial_is_on_call_time_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = \common\models\SettingCategory::getOrCreateByName('Redial');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'lead_redial_is_on_call_check_time',
                's_name' => 'Lead redial check is on call user',
                's_type' => \common\models\Setting::TYPE_INT,
                's_value' => 20,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
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
            'lead_redial_is_on_call_check_time',
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
