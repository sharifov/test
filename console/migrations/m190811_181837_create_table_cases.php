<?php

use yii\db\Migration;

/**
 * Class m190811_181837_create_table_cases
 */
class m190811_181837_create_table_cases extends Migration
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

        $this->createTable('{{%cases}}', [
            'cs_id' => $this->primaryKey(),
            'cs_subject' => $this->string(255),
            'cs_description' => $this->text(),
            'cs_category' => $this->string(50),
            'cs_status' => $this->integer()->notNull(),
            'cs_user_id' => $this->integer(),
            'cs_lead_id' => $this->integer(),
            'cs_call_id' => $this->integer(),
            'cs_dep_id' => $this->integer(),
            'cs_project_id' => $this->integer(),
            'cs_client_id' => $this->integer(),
            'cs_created_dt' => $this->dateTime(),
            'cs_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->createTable('{{%cases_category}}', [
            'cc_key' => $this->string(50)->notNull(),
            'cc_name' => $this->string(255)->notNull(),
            'cc_dep_id' => $this->integer()->notNull(),
            'cc_system' => $this->boolean()->defaultValue(false),
            'cc_created_dt' => $this->dateTime(),
            'cc_updated_dt' => $this->dateTime(),
            'cc_updated_user_id' => $this->integer(),
        ], $tableOptions);

        $this->addPrimaryKey(
            'PK-cases_category_cs_key',
            '{{%cases_category}}',
            ['cc_key']
            );

        $this->addForeignKey(
            'FK-cases_cs_category',
            '{{%cases}}',
            'cs_category',
            '{{%cases_category}}',
            'cc_key',
            'SET NULL',
            'CASCADE'
            );

        $this->addForeignKey(
            'FK-cases_category_cc_user_id',
            '{{%cases_category}}',
            'cc_updated_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-cases_category_cc_dep_id',
            '{{%cases_category}}',
            'cc_dep_id',
            '{{%department}}',
            'dep_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-cases_cs_user_id',
            '{{%cases}}',
            'cs_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
            );

        $this->addForeignKey(
            'FK-cases_cs_lead_id',
            '{{%cases}}',
            'cs_lead_id',
            '{{%leads}}',
            'id',
            'SET NULL',
            'CASCADE'
            );

        $this->addForeignKey(
            'FK-cases_cs_call_id',
            '{{%cases}}',
            'cs_call_id',
            '{{%call}}',
            'c_id',
            'SET NULL',
            'CASCADE'
            );

        $this->addForeignKey(
            'FK-cases_cs_dep_id',
            '{{%cases}}',
            'cs_dep_id',
            '{{%department}}',
            'dep_id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-cases_cs_client_id',
            '{{%cases}}',
            'cs_client_id',
            '{{%clients}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-cases_cs_category', '{{%cases}}');
        $this->dropTable('{{%cases_category}}');
        $this->dropTable('{{%cases}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
