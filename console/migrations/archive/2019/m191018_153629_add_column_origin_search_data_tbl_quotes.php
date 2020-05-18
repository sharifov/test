<?php

use yii\db\Migration;

/**
 * Class m191018_153629_add_column_origin_search_data_tbl_quotes
 */
class m191018_153629_add_column_origin_search_data_tbl_quotes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%quotes}}', 'origin_search_data', $this->text());

        Yii::$app->db->getSchema()->refreshTableSchema('{{%quotes}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%quotes}}', 'origin_search_data');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%quotes}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
