<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m191115_082352_table_settings_add_redial_calls_to_bugged
 */
class m191115_082352_table_settings_add_redial_calls_to_bugged extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'redial_calls_bugged',
            's_name' => 'Redial calls to bugged',
            's_type' => Setting::TYPE_INT,
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
            'redial_calls_bugged'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
