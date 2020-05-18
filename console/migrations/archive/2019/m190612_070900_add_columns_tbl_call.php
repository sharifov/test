<?php

use yii\db\Migration;

/**
 * Class m190612_070900_add_columns_tbl_call
 */
class m190612_070900_add_columns_tbl_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-call_c_parent_call_sid', '{{%call}}', ['c_parent_call_sid']);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-call_c_parent_call_sid', '{{%call}}');


        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');
    }

}
