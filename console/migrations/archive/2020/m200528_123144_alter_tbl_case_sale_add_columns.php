<?php

use yii\db\Migration;

/**
 * Class m200528_123144_alter_tbl_case_sale_add_columns
 */
class m200528_123144_alter_tbl_case_sale_add_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->truncateTable('{{%sale_ticket}}');

        $this->alterColumn('{{%sale_ticket}}', 'st_penalty_type', $this->tinyInteger(2));

        $this->addColumn('{{%case_sale}}', 'css_penalty_type', $this->tinyInteger(2));
        $this->addColumn('{{%case_sale}}', 'css_departure_dt', $this->dateTime());

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
        $this->alterColumn('{{%sale_ticket}}', 'st_penalty_type', $this->string(30));

        $this->dropColumn('{{%case_sale}}', 'css_penalty_type');
        $this->dropColumn('{{%case_sale}}', 'css_departure_dt');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%case_sale}}');
    }
}
