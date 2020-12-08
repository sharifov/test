<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m201110_103658_create_tbl_client_chat_form
 */
class m201110_103658_create_tbl_client_chat_forms extends Migration
{

    private $routes = [
        '/client-chat-forms-crud/view',
        '/client-chat-forms-crud/index',
        '/client-chat-forms-crud/create',
        '/client-chat-forms-crud/update',
        '/client-chat-forms-crud/builder',
        '/client-chat-forms-crud/delete',
    ];

    private $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%client_chat_form}}', [
            'ccf_id' => $this->primaryKey(),
            'ccf_key' => $this->string(100)->unique()->notNull(),
            'ccf_name' => $this->string(100),
            'ccf_project_id' => $this->integer(),
            'ccf_dataform_json' => $this->json(),
            'ccf_enabled' => $this->boolean()->defaultValue(true),
            'ccf_created_user_id' => $this->integer(),
            'ccf_updated_user_id' => $this->integer(),
            'ccf_created_dt' => $this->dateTime(),
            'ccf_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-client_chat_form-ccf_project_id',
            '{{%client_chat_form}}',
            'ccf_project_id',
            '{{%projects}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-client_chat_form-ccf_created_user_id',
            '{{%client_chat_form}}',
            'ccf_created_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-client_chat_form-ccf_updated_user_id',
            '{{%client_chat_form}}',
            'ccf_updated_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%client_chat_form}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
