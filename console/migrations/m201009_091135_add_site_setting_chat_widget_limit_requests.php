<?php

use yii\db\Migration;

/**
 * Class m201009_091135_add_site_setting_chat_widget_limit_requests
 */
class m201009_091135_add_site_setting_chat_widget_limit_requests extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'chat_widget_limit_requests',
            's_name' => 'Number of displayed requests',
            's_type' => \common\models\Setting::TYPE_INT,
            's_value' => 20,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
        ]);

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
            'chat_widget_limit_requests'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
