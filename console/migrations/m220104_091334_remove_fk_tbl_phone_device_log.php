<?php

use yii\db\Migration;

/**
 * Class m220104_091334_remove_fk_tbl_phone_device_log
 */
class m220104_091334_remove_fk_tbl_phone_device_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('FK-phone_device_log_device', '{{%phone_device_log}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
