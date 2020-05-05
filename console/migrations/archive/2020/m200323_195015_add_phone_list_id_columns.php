<?php

use yii\db\Migration;

/**
 * Class m200323_195015_add_phone_list_id_columns
 */
class m200323_195015_add_phone_list_id_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%department_phone_project}}', 'dpp_phone_list_id', $this->integer()->null());
        $this->addForeignKey(
            'FK-department_phone_project-dpp_phone_list_id',
            '{{%department_phone_project}}',
            'dpp_phone_list_id',
            '{{%phone_list}}',
            'pl_id',
            'SET NULL',
            'CASCADE'
        );
        $this->addColumn('{{%user_project_params}}', 'upp_phone_list_id', $this->integer()->null());
        $this->addForeignKey(
            'FK-user_project_params-upp_phone_list_id',
            '{{%user_project_params}}',
            'upp_phone_list_id',
            '{{%phone_list}}',
            'pl_id',
            'SET NULL',
            'CASCADE'
        );
        $schema = Yii::$app->db->getSchema();
        $schema->refreshTableSchema('{{%department_phone_project}}');
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
        $this->dropForeignKey('FK-user_project_params-upp_phone_list_id','{{%user_project_params}}');
        $this->dropForeignKey('FK-department_phone_project-dpp_phone_list_id','{{%department_phone_project}}');
        $this->dropColumn('{{%department_phone_project}}', 'dpp_phone_list_id');
        $this->dropColumn('{{%user_project_params}}', 'upp_phone_list_id');
        $schema = Yii::$app->db->getSchema();
        $schema->refreshTableSchema('{{%department_phone_project}}');
        $schema->refreshTableSchema('{{%user_project_params}}');
        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }
}
