<?php

use yii\db\Migration;

/**
 * Class m190903_101033_add_column_client_id_tbl_call
 */
class m190903_101033_add_column_client_id_tbl_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%call}}', 'c_client_id', $this->integer());
        $this->addForeignKey('FK-call_c_client_id', '{{%call}}', ['c_client_id'], '{{%clients}}', ['id'], 'SET NULL', 'CASCADE');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-call_c_client_id', '{{%call}}');

        $this->dropColumn('{{%call}}', 'c_client_id');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
