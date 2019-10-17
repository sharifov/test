<?php

use yii\db\Migration;

/**
 * Class m191009_120146_table_settings_add_followup_settings
 */
class m191009_120146_table_settings_add_followup_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'follow_up_lookup_days',
            's_name' => 'Follow up lookup days',
            's_type' => 'int',
            's_value' => 1,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'follow_up_call_min_time',
            's_name' => 'Follow up call min time',
            's_type' => 'int',
            's_value' => 1,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'follow_up_call_min_count',
            's_name' => 'Follow up call min count',
            's_type' => 'int',
            's_value' => 1,
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
            'follow_up_lookup_days', 'follow_up_call_min_time', 'follow_up_call_min_count'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
