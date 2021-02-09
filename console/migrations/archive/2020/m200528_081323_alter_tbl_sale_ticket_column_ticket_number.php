<?php

use yii\db\Migration;

/**
 * Class m200528_081323_alter_tbl_sale_ticket_column_ticket_number
 */
class m200528_081323_alter_tbl_sale_ticket_column_ticket_number extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%sale_ticket}}', 'st_ticket_number', $this->string(30));

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%sale_ticket}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%sale_ticket}}', 'st_ticket_number', $this->string(15));

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%sale_ticket}}');
    }
}
