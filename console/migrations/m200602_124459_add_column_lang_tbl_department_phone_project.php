<?php

use yii\db\Migration;

/**
 * Class m200602_124459_add_column_lang_tbl_department_phone_project
 */
class m200602_124459_add_column_lang_tbl_department_phone_project extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        //$this->dropColumn('{{%department_phone_project}}', 'dpp_language_id');

        $this->addColumn('{{%department_phone_project}}', 'dpp_language_id', $this->string(5)->append('CHARACTER SET utf8 COLLATE utf8_unicode_ci'));
        $this->addForeignKey('FK-department_phone_project-dpp_language_id',
            '{{%department_phone_project}}', 'dpp_language_id',
            '{{%language}}', 'language_id', 'SET NULL', 'CASCADE');


        $this->addColumn('{{%call}}', 'c_language_id', $this->string(5)->append('CHARACTER SET utf8 COLLATE utf8_unicode_ci'));
        $this->addForeignKey('FK-call-c_language_id',
            '{{%call}}', 'c_language_id',
            '{{%language}}', 'language_id', 'SET NULL', 'CASCADE');


        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
        Yii::$app->db->getSchema()->refreshTableSchema('{{%department_phone_project}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%department_phone_project}}', 'dpp_language_id');
        $this->dropColumn('{{%call}}', 'c_language_id');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
        Yii::$app->db->getSchema()->refreshTableSchema('{{%department_phone_project}}');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');
    }

}
