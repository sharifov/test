<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m201021_123354_add_new_site_settings_soft_close
 */
class m201021_123354_add_new_site_settings_soft_close extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'client_chat_soft_close_enabled',
                's_name' => 'Client Chat Soft Close Enable - true/false',
                's_type' => Setting::TYPE_BOOL,
                's_value' => true,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => null,
            ]
        );

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'client_chat_soft_close_timeout_minutes',
                's_name' => 'Client Chat Soft Close Timeout minutes',
                's_type' => Setting::TYPE_INT,
                's_value' => 20,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => null,
            ]
        );

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'client_chat_soft_close_enabled'
        ]]);

        $this->delete('{{%setting}}', ['IN', 's_key', [
            'client_chat_soft_close_timeout_minutes'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
