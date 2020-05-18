<?php

use yii\db\Migration;

/**
 * Class m190807_124210_add_column_tikets_tbl_quotes
 */
class m190807_124210_add_column_tikets_tbl_quotes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%quotes}}', 'tickets', $this->text());

        $this->addColumn('{{%quote_segment}}', 'qs_ticket_id', $this->smallInteger());

        Yii::$app->db->getSchema()->refreshTableSchema('{{%quotes}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%quote_segment}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%quotes}}', 'tickets');
        $this->dropColumn('{{%quote_segment}}', 'qs_ticket_id');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%quotes}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%quote_segment}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
