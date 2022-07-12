<?php

use yii\db\Migration;

/**
 * Class m220712_105957_add_new_client_data_key
 */
class m220712_105957_add_new_client_data_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand()->upsert('{{%client_data_key}}', [
            'cdk_key' => 'client_return',
            'cdk_name' => 'Client Return Indication',
            'cdk_enable' => true,
            'cdk_is_system' => true,
            'cdk_description' => 'Client Return Indication',
        ])->execute();

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%client_data_key}}', ['IN', 'cdk_key', [
            'client_return'
        ]]);
    }
}
