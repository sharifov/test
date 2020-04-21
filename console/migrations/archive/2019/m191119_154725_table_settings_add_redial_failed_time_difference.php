<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m191119_154725_table_settings_add_redial_failed_time_difference
 */
class m191119_154725_table_settings_add_redial_failed_time_difference extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'redial_failed_time_difference',
            's_name' => 'Redial failed time difference',
            's_type' => Setting::TYPE_INT,
            's_value' => 1,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
        ]);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');

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
            'redial_failed_time_difference'
        ]]);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
