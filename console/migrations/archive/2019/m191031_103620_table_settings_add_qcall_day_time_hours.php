<?php

use yii\db\Migration;

/**
 * Class m191031_103620_table_settings_add_day_time_hours
 */
class m191031_103620_table_settings_add_qcall_day_time_hours extends Migration
{

    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'qcall_day_time_hours',
            's_name' => 'Qcall day time hours',
            's_type' => 'string',
            's_value' => '9:00;21:00',
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
        ]);
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
            'qcall_day_time_hours'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
