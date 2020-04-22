<?php

use yii\db\Migration;

/**
 * Class m190913_064947_add_jivo_chat_params
 */
class m190913_064947_add_jivo_chat_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'jivo_chat_id',
            's_name' => 'Jivo chat Id',
            's_type' => 'int',
            's_value' => 52,
            's_updated_dt' => date('Y-m-d H:i:s'),
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
            'jivo_chat_id'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
