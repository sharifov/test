<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m220513_121621_add_client_chat_debug_settings
 */
class m220513_121621_add_client_chat_debug_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Client Chat');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'client_chat_debug_enable',
                's_name' => 'Client chat debug',
                's_type' => Setting::TYPE_BOOL,
                's_value' => 0,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'Client chat debug',
            ]
        );

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'client_chat_debug_enable',
        ]]);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
