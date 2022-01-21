<?php

use yii\db\Migration;

/**
 * Class m210504_120011_alter_column_sale_book_id_in_case_sale_tbl
 */
class m210504_120011_alter_column_sale_book_id_in_case_sale_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%case_sale}}', 'css_sale_book_id', $this->string(20));

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%case_sale}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%case_sale}}', 'css_sale_book_id', $this->string(8));

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%case_sale}}');
    }
}
