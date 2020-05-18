<?php

use yii\db\Migration;

/**
 * Class m190813_131817_add_column_tbl_call
 */
class m190813_131817_add_column_tbl_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%call}}', 'c_case_id', $this->integer());
        $this->addForeignKey('FK-call_c_case_id', '{{%call}}', ['c_case_id'], '{{%cases}}', ['cs_id'], 'SET NULL', 'CASCADE');
        $this->createIndex('IND-call_c_case_id', '{{%call}}', ['c_case_id']);

        $this->addColumn('{{%sms}}', 's_case_id', $this->integer());
        $this->addForeignKey('FK-sms_s_case_id', '{{%sms}}', ['s_case_id'], '{{%cases}}', ['cs_id'], 'SET NULL', 'CASCADE');
        $this->createIndex('IND-sms_s_case_id', '{{%sms}}', ['s_case_id']);


        $this->addColumn('{{%email}}', 'e_case_id', $this->integer());
        $this->addForeignKey('FK-email_e_case_id', '{{%email}}', ['e_case_id'], '{{%cases}}', ['cs_id'], 'SET NULL', 'CASCADE');
        $this->createIndex('IND-email_e_case_id', '{{%email}}', ['e_case_id']);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%sms}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%email}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-call_c_case_id', '{{%call}}');
        $this->dropColumn('{{%call}}', 'c_case_id');

        $this->dropForeignKey('FK-sms_s_case_id', '{{%sms}}');
        $this->dropColumn('{{%sms}}', 's_case_id');

        $this->dropForeignKey('FK-email_e_case_id', '{{%email}}');
        $this->dropColumn('{{%email}}', 'e_case_id');


        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%sms}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%email}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
