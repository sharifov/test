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

		'/client-chat-crud/create',
		'/client-chat-crud/update',
		'/client-chat-crud/delete',
		'/client-chat-crud/view',
		'/client-chat-crud/index',
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
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
		}

		$this->createTable('{{%client_chat_request}}', [
			'ccr_id' => $this->primaryKey(),
			'ccr_event' => $this->string(50),
			'ccr_json_data' => $this->text(),
			'ccr_created_dt' => $this->dateTime()
		], $tableOptions);

		$this->createTable('{{%client_chat}}', [
			'cch_id' => $this->primaryKey(),
			'cch_rid' => $this->string(150),
			'cch_ccr_id' => $this->integer(),
			'cch_title' => $this->string(50),
			'cch_description' => $this->string(),
			'cch_project_id' => $this->integer(),
			'cch_dep_id' => $this->integer(),
			'cch_channel_id' => $this->integer(),
			'cch_client_id' => $this->integer(),
			'cch_owner_user_id'  => $this->integer(),
			'cch_case_id' => $this->integer(),
			'cch_lead_id' => $this->integer(),
			'cch_note' => $this->string(),
			'cch_status_id' => $this->tinyInteger(),
			'cch_ip' => $this->string(20),
			'cch_ua' => $this->integer(),
			'cch_language_id' => $this->string(5),
			'cch_created_dt' => $this->dateTime(),
			'cch_updated_dt' => $this->dateTime(),
			'cch_created_user_id' => $this->integer(),
			'cch_updated_user_id' => $this->integer()
		], $tableOptions);

		$this->addForeignKey('FK-cch_ccr_id', '{{%client_chat}}', ['cch_ccr_id'], '{{%client_chat_request}}', ['ccr_id'], 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-cch_project_id', '{{%client_chat}}', ['cch_project_id'], '{{%projects}}', ['id'], 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-cch_dep_id', '{{%client_chat}}', ['cch_dep_id'], '{{%department}}', ['dep_id'], 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-cch_client_id', '{{%client_chat}}', ['cch_client_id'], '{{%clients}}', ['id'], 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-cch_owner_user_id', '{{%client_chat}}', ['cch_owner_user_id'], '{{%employees}}', ['id'], 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-cch_case_id', '{{%client_chat}}', ['cch_case_id'], '{{%cases}}', ['cs_id'], 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-cch_lead_id', '{{%client_chat}}', ['cch_lead_id'], '{{%leads}}', ['id'], 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-cch_language_id', '{{%client_chat}}', ['cch_language_id'], '{{%language}}', ['language_id'], 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-cch_created_user_id', '{{%client_chat}}', ['cch_created_user_id'], '{{%employees}}', ['id'], 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-cch_updated_user_id', '{{%client_chat}}', ['cch_updated_user_id'], '{{%employees}}', ['id'], 'CASCADE', 'CASCADE');

		(new RbacMigrationService())->up($this->routes, $this->roles);
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropForeignKey('FK-cch_ccr_id', '{{%client_chat}}');
		$this->dropForeignKey('FK-cch_project_id', '{{%client_chat}}');
		$this->dropForeignKey('FK-cch_dep_id', '{{%client_chat}}');
		$this->dropForeignKey('FK-cch_client_id', '{{%client_chat}}');
		$this->dropForeignKey('FK-cch_owner_user_id', '{{%client_chat}}');
		$this->dropForeignKey('FK-cch_case_id', '{{%client_chat}}');
		$this->dropForeignKey('FK-cch_lead_id', '{{%client_chat}}');
		$this->dropForeignKey('FK-cch_language_id', '{{%client_chat}}');
		$this->dropForeignKey('FK-cch_created_user_id', '{{%client_chat}}');
		$this->dropForeignKey('FK-cch_updated_user_id', '{{%client_chat}}');
		$this->dropTable('{{%client_chat}}');

		$this->dropTable('{{%client_chat_request}}');

		(new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
