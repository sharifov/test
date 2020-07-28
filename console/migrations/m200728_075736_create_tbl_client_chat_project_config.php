<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200728_075736_create_tbl_client_chat_project_config
 */
class m200728_075736_create_tbl_client_chat_project_config extends Migration
{
    public $route = [
        '/client-chat-project-config/*',
    ];

    public $roles = [
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

        $this->createTable('{{%client_chat_project_config}}', [
            'ccpc_project_id' => $this->integer()->notNull(),
            'ccpc_params_json' => $this->json(),
            'ccpc_theme_json' => $this->json(),
            'ccpc_registration_json' => $this->json(),
            'ccpc_settings_json' => $this->json(),
            'ccpc_enabled' => $this->boolean()->defaultValue(true),
            'ccpc_created_user_id' => $this->integer(),
            'ccpc_updated_user_id' => $this->integer(),
            'ccpc_created_dt' => $this->dateTime(),
            'ccpc_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-client_chat_project_config-ccpc_project_id', '{{%client_chat_project_config}}', ['ccpc_project_id']);
        $this->addForeignKey('FK-client_chat_project_config-ccpc_project_id', '{{%client_chat_project_config}}', ['ccpc_project_id'], '{{%projects}}', ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-client_chat_project_config-ccpc_created_user_id', '{{%client_chat_project_config}}', ['ccpc_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-client_chat_project_config-ccpc_updated_user_id', '{{%client_chat_project_config}}', ['ccpc_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

        (new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%client_chat_project_config}}');
        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
    }
}
