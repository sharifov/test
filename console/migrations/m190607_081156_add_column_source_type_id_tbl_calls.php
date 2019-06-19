<?php

use yii\db\Migration;

/**
 * Class m190607_081156_add_column_source_type_id_tbl_calls
 */
class m190607_081156_add_column_source_type_id_tbl_calls extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%call}}', 'c_source_type_id', $this->tinyInteger());
        $this->createIndex('IND-call_c_source_type_id', '{{%call}}', ['c_source_type_id']);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');



        $this->insert('{{%task}}', [
            't_category_id' => 0,
            't_description' => 'Missed Call',
            't_hidden' => 0,
            't_key' => 'missed-call',
            't_name' => 'Missed Call',
            't_sort_order' => 2,
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-call_c_source_type_id', '{{%call}}');
        $this->dropColumn('{{%call}}', 'c_source_type_id');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');

        $this->delete('{{%task}}', ['t_key' => 'missed-call']);
    }

}
