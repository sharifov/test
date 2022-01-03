<?php

use yii\db\Migration;

/**
 * Class m220103_083842_add_column_buid_tbl_phone_devices
 */
class m220103_083842_add_column_buid_tbl_phone_devices extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('IDX-phone_device_group', '{{%phone_device}}');
        $this->addColumn('{{%phone_device}}', 'pd_buid', $this->string(10));
        $this->createIndex('IDX-phone_device_group', '{{%phone_device}}', ['pd_id', 'pd_user_id', 'pd_connection_id', 'pd_buid']);
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
        $this->dropColumn('{{%phone_device}}', 'pd_buid');
        $this->createIndex('IDX-phone_device_group', '{{%phone_device}}', ['pd_id', 'pd_user_id', 'pd_connection_id', 'pd_user_agent']);
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
