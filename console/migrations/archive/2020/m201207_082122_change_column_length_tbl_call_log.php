<?php

use yii\db\Migration;

/**
 * Class m201207_082122_change_column_length_tbl_call_log
 */
class m201207_082122_change_column_length_tbl_call_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%call_log}}', 'cl_phone_from', $this->string(30)->null());
        $this->alterColumn('{{%call_log}}', 'cl_phone_to', $this->string(30)->null());
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
        $this->alterColumn('{{%call_log}}', 'cl_phone_from', $this->string(18)->null());
        $this->alterColumn('{{%call_log}}', 'cl_phone_to', $this->string(18)->null());
        Yii::$app->db->getSchema()->refreshTableSchema('{{%call_log}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
