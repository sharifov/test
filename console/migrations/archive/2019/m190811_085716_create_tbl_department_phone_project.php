<?php

use yii\db\Migration;

/**
 * Class m190811_085716_create_tbl_department_phone_project
 */
class m190811_085716_create_tbl_department_phone_project extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%department_phone_project}}', [
            'dpp_id' => $this->primaryKey(),
            'dpp_phone_number' => $this->string(18)->notNull()->unique(),
            'dpp_project_id' => $this->integer()->notNull(),
            'dpp_dep_id' => $this->integer(),
            'dpp_source_id' => $this->integer(),
            'dpp_params' => $this->json(),
            'dpp_ivr_enable' => $this->boolean()->defaultValue(false),
            'dpp_enable' => $this->boolean()->defaultValue(true),
            'dpp_updated_user_id' => $this->integer(),
            'dpp_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        //$this->addPrimaryKey('PK-department_phone_project', '{{%department_phone_project}}', ['dpp_dep_id', 'dpp_project_id', 'dpp_phone_number']);

        $this->createIndex('IND-department_phone_project_dpp_phone_number', '{{%department_phone_project}}', ['dpp_phone_number']);

        $this->addForeignKey('FK-department_phone_project_dpp_dep_id', '{{%department_phone_project}}', ['dpp_dep_id'], '{{%department}}', ['dep_id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-department_phone_project_dpp_project_id', '{{%department_phone_project}}', ['dpp_project_id'], '{{%projects}}', ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-department_phone_project_dpp_source_id', '{{%department_phone_project}}', ['dpp_source_id'], '{{%sources}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-department_phone_project_dpp_updated_user_id', '{{%department_phone_project}}', ['dpp_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');


        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%department_phone_project}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
