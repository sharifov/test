<?php

use yii\db\Migration;

/**
 * Class m200219_154510_add_index_cs_source_type_id_tbl_cases
 */
class m200219_154510_add_index_cs_source_type_id_tbl_cases extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-cases-cs_source_type_id', '{{%cases}}', ['cs_source_type_id'], false);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%cases}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-cases-cs_source_type_id', '{{%cases}}');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%cases}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
