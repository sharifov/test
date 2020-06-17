<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200617_093813_create_new_tables_for_client_chat
 */
class m200617_093813_create_new_tables_for_client_chat extends Migration
{
	private $routes = [
		'/client-chat-request-crud/create',
		'/client-chat-request-crud/update',
		'/client-chat-request-crud/delete',
		'/client-chat-request-crud/view',
		'/client-chat-request-crud/index',
	];

	private $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
		Employee::ROLE_AGENT,
		Employee::ROLE_SUPERVISION,
		Employee::ROLE_QA,
		Employee::ROLE_USER_MANAGER,
		Employee::ROLE_SUP_AGENT,
		Employee::ROLE_SUP_SUPER,
		Employee::ROLE_EX_AGENT,
		Employee::ROLE_EX_SUPER,
		Employee::ROLE_SALES_SENIOR,
		Employee::ROLE_EXCHANGE_SENIOR,
		Employee::ROLE_SUPPORT_SENIOR,
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

		$this->createTable('{{%client_chat_request}}', [
			'ccr_id' => $this->primaryKey(),
			'ccr_event' => $this->string(50),
			'ccr_json_data' => $this->text(),
			'ccr_created_dt' => $this->dateTime()
		], $tableOptions);

		(new RbacMigrationService())->up($this->routes, $this->roles);
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropTable('{{%client_chat_request}}');

		(new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
