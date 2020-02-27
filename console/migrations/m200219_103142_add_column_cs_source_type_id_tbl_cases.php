<?php

use yii\db\Migration;

/**
 * Class m200219_103142_add_column_cs_source_type_id_tbl_cases
 */
class m200219_103142_add_column_cs_source_type_id_tbl_cases extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%cases}}', 'cs_source_type_id', $this->tinyInteger()->null());

        Yii::$app->db->getSchema()->refreshTableSchema('{{%cases}}');

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%cases}}', 'cs_source_type_id');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%cases}}');

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }
}
