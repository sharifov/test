<?php

use yii\db\Migration;

/**
 * Class m200331_093510_alter_columns_phones_tbl_call_log
 */
class m200331_093510_alter_columns_phones_tbl_call_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%call_log}}', 'cl_phone_from', $this->string(18)->null());
        $this->alterColumn('{{%call_log}}', 'cl_phone_to', $this->string(18)->null());
        Yii::$app->db->getSchema()->refreshTableSchema('{{%call_log}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%call_log}}', 'cl_phone_from', $this->string(15)->null());
        $this->alterColumn('{{%call_log}}', 'cl_phone_to', $this->string(15)->null());
        Yii::$app->db->getSchema()->refreshTableSchema('{{%call_log}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
