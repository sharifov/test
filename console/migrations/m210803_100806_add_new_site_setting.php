<?php

use yii\db\Migration;

/**
 * Class m210803_100806_add_new_site_setting
 */
class m210803_100806_add_new_site_setting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = \common\models\SettingCategory::getOrCreateByName('Client Chat');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'client_chat_api_log_enabled',
                's_name' => 'Enable api log',
                's_type' => \common\models\Setting::TYPE_BOOL,
                's_value' => 60,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'Enable/disable saving logs in api_log table on Client Chat api requests',
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
            'client_chat_api_log_enabled'
        ]]);
    }
}
