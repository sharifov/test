<?php

use yii\db\Migration;

/**
 * Class m220103_070849_add_index_to_phone_device_tbl
 */
class m220103_070849_add_index_to_phone_device_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IDX-phone_device_group', '{{%phone_device}}', ['pd_id', 'pd_user_id', 'pd_connection_id', 'pd_user_agent']);
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IDX-phone_device_group', '{{%phone_device}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
