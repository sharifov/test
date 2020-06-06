<?php

use yii\db\Migration;

/**
 * Class m200604_171000_add_column_type_tbl_conference_participant
 */
class m200604_171000_add_column_type_tbl_conference_participant extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%call}}', 'c_conference_id', $this->integer());
        $this->createIndex('IND-call-c_conference_id', '{{%call}}', ['c_conference_id']);

        $this->addColumn('{{%conference_participant}}', 'cp_type_id', $this->tinyInteger(1));

        $this->addColumn('{{%conference}}', 'cf_created_user_id', $this->integer());
        $this->addForeignKey('FK-conference-cf_created_user_id', '{{%conference}}', ['cf_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%conference_participant}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%conference}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-conference-cf_created_user_id', '{{%conference}}');
        $this->dropColumn('{{%conference}}', 'cf_created_user_id');

        $this->dropColumn('{{%conference_participant}}', 'cp_type_id');

        $this->dropIndex('IND-call-c_conference_id', '{{%call}}');
        $this->dropColumn('{{%call}}', 'c_conference_id');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%conference_participant}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%conference}}');
    }
}
