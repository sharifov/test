<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m191115_124950_table_settings_add_redial_default_phone_history_hours
 */
class m191115_124950_table_settings_add_redial_default_phone_history_hours extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'redial_default_phone_history_hours',
            's_name' => 'Redial default phone history hours',
            's_type' => Setting::TYPE_INT,
            's_value' => 1,
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
            'redial_default_phone_history_hours'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
