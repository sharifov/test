<?php

use yii\db\Migration;

/**
 * Class m220407_104819_add_new_lead_data_key_created_call_id
 */
class m220407_104819_add_new_lead_data_key_created_call_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand()->upsert('{{%lead_data_key}}', [
            'ldk_key' => 'created_by_call_id',
            'ldk_name' => 'Created by Call ID',
            'ldk_enable' => true,
            'ldk_is_system' => true,
            'ldk_created_dt' => date('Y-m-d H:i:s')
        ])->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%lead_data_key}}', ['IN', 'ldk_key', [
            'created_by_call_id'
        ]]);
    }
}
