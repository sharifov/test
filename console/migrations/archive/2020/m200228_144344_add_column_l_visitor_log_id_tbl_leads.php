<?php

use yii\db\Migration;

/**
 * Class m200228_144344_add_column_l_visitor_log_id_tbl_leads
 */
class m200228_144344_add_column_l_visitor_log_id_tbl_leads extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%leads}}', 'l_visitor_log_id', $this->integer()->null());

        $this->addForeignKey('FK-leads-l_visitor_log_id', '{{%leads}}', 'l_visitor_log_id', '{{%visitor_log}}', 'vl_id', 'SET NULL', 'CASCADE');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%leads}}');

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-leads-l_visitor_log_id', '{{%leads}}');

        $this->dropColumn('{{%leads}}', 'l_visitor_log_id');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%leads}}');

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }
}
