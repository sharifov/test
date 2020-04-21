<?php

use yii\db\Migration;

/**
 * Class m191204_090159_alter_column_tbl_product_quote
 */
class m191204_090159_alter_column_tbl_product_quote extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('{{%product_quote}}',	'pr_name', 'pq_name');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%product_quote}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('{{%product_quote}}',	'pq_name', 'pr_name');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%product_quote}}');
    }
}
