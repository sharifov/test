<?php

use yii\db\Migration;

/**
 * Class m211129_084841_add_client_data_key_app_call_total_count
 */
class m211129_084841_add_client_data_key_app_call_total_count extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand()->upsert('{{%client_data_key}}', [
            'cdk_key' => 'app_call_out_total_count',
            'cdk_name' => 'App Call Out Total Count',
            'cdk_enable' => true,
            'cdk_is_system' => true,
            'cdk_description' => 'Number of calls from agent to client',
        ])->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%client_data_key}}', ['IN', 'cdk_key', [
            'app_call_out_total_count'
        ]]);
    }
}
