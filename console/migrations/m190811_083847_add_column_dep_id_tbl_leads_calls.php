<?php

use yii\db\Migration;

/**
 * Class m190811_083847_add_column_dep_id_tbl_leads_calls
 */
class m190811_083847_add_column_dep_id_tbl_leads_calls extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%leads}}', 'l_dep_id', $this->integer());
        $this->addColumn('{{%call}}', 'c_dep_id', $this->integer());

        $this->addForeignKey('FK-leads_l_dep_id', '{{%leads}}', ['l_dep_id'], '{{%department}}', ['dep_id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-call_c_dep_id', '{{%call}}', ['c_dep_id'], '{{%department}}', ['dep_id'], 'SET NULL', 'CASCADE');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%leads}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%leads}}', 'l_dep_id');
        $this->dropColumn('{{%call}}', 'c_dep_id');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%leads}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
