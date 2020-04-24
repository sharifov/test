<?php

use yii\db\Migration;

/**
 * Class m200424_043826_add_columns_to_client
 */
class m200424_043826_add_columns_to_client extends Migration
{
    /**
     * {@inheritdoc}
     */
     public function safeUp()
    {
        $this->addColumn('{{%clients}}', 'parent_id', $this->integer());
        $this->addColumn('{{%clients}}', 'is_company', $this->boolean());
        $this->addColumn('{{%clients}}', 'is_public', $this->boolean());
        $this->addColumn('{{%clients}}', 'company_name', $this->string(150));
        $this->addColumn('{{%clients}}', 'description', $this->text());
        $this->addColumn('{{%clients}}', 'disabled', $this->boolean()->defaultValue(false));
        $this->addColumn('{{%clients}}', 'rating', $this->tinyInteger());

        $this->alterColumn('{{%clients}}', 'first_name', $this->string(100));

        $this->createIndex('IND-clients-first_name', '{{%clients}}', ['first_name']);
        $this->createIndex('IND-clients-last_name', '{{%clients}}', ['last_name']);
        $this->createIndex('IND-clients-company_name', '{{%clients}}', ['company_name']);

        $this->addForeignKey('FK-clients-id_parent_id',
            '{{%clients}}', 'parent_id',
            '{{%clients}}', 'id', 'SET NULL', 'CASCADE');

        $this->addColumn('{{%client_phone}}', 'cp_title', $this->string(100));
        $this->addColumn('{{%client_email}}', 'ce_title', $this->string(100));

        $this->createTable('{{%user_contact_list}}',    [
            'ucl_user_id' => $this->integer()->notNull(),
            'ucl_client_id' => $this->integer()->notNull(),
            'ucl_title' => $this->string(100),
            'ucl_description' => $this->text(),
            'ucl_created_dt' => $this->dateTime(),
        ]);

        $this->addPrimaryKey('PK-user_contact_list', '{{%user_contact_list}}', ['ucl_user_id', 'ucl_client_id']);
        $this->addForeignKey('FK-user_contact_list-ucl_ucl_user_id',
            '{{%user_contact_list}}', 'ucl_user_id',
            '{{%employees}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-user_contact_list-ucl_client_id',
            '{{%user_contact_list}}', 'ucl_client_id',
            '{{%clients}}', 'id', 'CASCADE', 'CASCADE');


        $this->createTable('{{%client_project}}',    [
            'cp_client_id' => $this->integer()->notNull(),
            'cp_project_id' => $this->integer()->notNull(),
            'cp_created_dt' => $this->dateTime(),
        ]);

        $this->addPrimaryKey('PK-client_project', '{{%client_project}}', ['cp_client_id', 'cp_project_id']);
        $this->addForeignKey('FK-client_project-cp_client_id',
            '{{%client_project}}', 'cp_client_id',
            '{{%clients}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-client_project_cp_project_id',
            '{{%client_project}}', 'cp_project_id',
            '{{%projects}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-clients-id_parent_id', '{{%clients}}');
        $this->dropIndex('IND-clients-first_name', '{{%clients}}');
        $this->dropIndex('IND-clients-last_name', '{{%clients}}');
        $this->dropIndex('IND-clients-company_name', '{{%clients}}');

        $this->dropColumn('{{%clients}}', 'parent_id');
        $this->dropColumn('{{%clients}}', 'is_company');
        $this->dropColumn('{{%clients}}', 'is_public');
        $this->dropColumn('{{%clients}}', 'company_name');
        $this->dropColumn('{{%clients}}', 'description');
        $this->dropColumn('{{%clients}}', 'disabled');
        $this->dropColumn('{{%clients}}', 'rating');

        $this->alterColumn('{{%clients}}', 'first_name', $this->string(100)->notNull());

        $this->dropColumn('{{%client_phone}}', 'cp_title');
        $this->dropColumn('{{%client_email}}', 'ce_title');

        $this->dropForeignKey('FK-user_contact_list-ucl_ucl_user_id', '{{%user_contact_list}}');
        $this->dropForeignKey('FK-user_contact_list-ucl_client_id', '{{%user_contact_list}}');
        $this->dropTable('{{%user_contact_list}}');

        $this->dropForeignKey('FK-client_project-cp_client_id', '{{%client_project}}');
        $this->dropForeignKey('FK-client_project_cp_project_id', '{{%client_project}}');
        $this->dropTable('{{%client_project}}');
    }
}
