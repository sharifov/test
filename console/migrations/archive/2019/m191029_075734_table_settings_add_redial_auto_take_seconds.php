<?php

use yii\db\Migration;

/**
 * Class m191029_075734_table_settings_add_redial_auto_take_seconds
 */
class m191029_075734_table_settings_add_redial_auto_take_seconds extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'redial_auto_take_seconds',
            's_name' => 'Redial auto take seconds',
            's_type' => 'int',
            's_value' => 10,
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
            'redial_auto_take_seconds'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }


}
