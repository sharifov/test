<?php

use yii\db\Migration;

/**
 * Class m200527_074703_alttbl_sale_ticket_column_penalty_amount
 */
class m200527_074703_alttbl_sale_ticket_column_penalty_amount extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%sale_ticket}}', 'st_penalty_amount', $this->string(50));

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
        $this->alterColumn('{{%sale_ticket}}', 'st_penalty_amount', $this->decimal(8, 2));

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%sale_ticket}}');
    }
}
