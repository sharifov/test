<?php

use yii\db\Migration;

/**
 * Class m200323_204153_m200323_195015_add_email_list_id_columns
 */
class m200323_204153_m200323_195015_add_email_list_id_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%department_email_project}}', 'dep_email_list_id', $this->integer()->null());
        $this->addForeignKey(
            'FK-department_email_project-dep_email_list_id',
            '{{%department_email_project}}',
            'dep_email_list_id',
            '{{%email_list}}',
            'el_id',
            'SET NULL',
            'CASCADE'
        );
        $this->addColumn('{{%user_project_params}}', 'upp_email_list_id', $this->integer()->null());
        $this->addForeignKey(
            'FK-user_project_params-upp_email_list_id',
            '{{%user_project_params}}',
            'upp_email_list_id',
            '{{%email_list}}',
            'el_id',
            'SET NULL',
            'CASCADE'
        );
        $schema = Yii::$app->db->getSchema();
        $schema->refreshTableSchema('{{%department_email_project}}');
        $schema->refreshTableSchema('{{%user_project_params}}');
        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-user_project_params-upp_email_list_id','{{%user_project_params}}');
        $this->dropForeignKey('FK-department_email_project-dep_email_list_id','{{%department_email_project}}');
        $this->dropColumn('{{%department_email_project}}', 'dep_email_list_id');
        $this->dropColumn('{{%user_project_params}}', 'upp_email_list_id');
        $schema = Yii::$app->db->getSchema();
        $schema->refreshTableSchema('{{%department_email_project}}');
        $schema->refreshTableSchema('{{%user_project_params}}');
        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }
}
