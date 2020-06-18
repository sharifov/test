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

		'/client-chat-channel-crud/create',
		'/client-chat-channel-crud/update',
		'/client-chat-channel-crud/delete',
		'/client-chat-channel-crud/view',
		'/client-chat-channel-crud/index',

		'/client-chat-status-log-crud/create',
		'/client-chat-status-log-crud/update',
		'/client-chat-status-log-crud/delete',
		'/client-chat-status-log-crud/view',
		'/client-chat-status-log-crud/index',

		'/client-chat-user-channel-crud/create',
		'/client-chat-user-channel-crud/update',
		'/client-chat-user-channel-crud/delete',
		'/client-chat-user-channel-crud/view',
		'/client-chat-user-channel-crud/index',

		'/client-chat-user-access-crud/create',
		'/client-chat-user-access-crud/update',
		'/client-chat-user-access-crud/delete',
		'/client-chat-user-access-crud/view',
		'/client-chat-user-access-crud/index',
	];

	private $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
//		Employee::ROLE_AGENT,
//		Employee::ROLE_SUPERVISION,
//		Employee::ROLE_QA,
//		Employee::ROLE_USER_MANAGER,
//		Employee::ROLE_SUP_AGENT,
//		Employee::ROLE_SUP_SUPER,
//		Employee::ROLE_EX_AGENT,
//		Employee::ROLE_EX_SUPER,
//		Employee::ROLE_SALES_SENIOR,
//		Employee::ROLE_EXCHANGE_SENIOR,
//		Employee::ROLE_SUPPORT_SENIOR,
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

		$this->addForeignKey('FK-cch_ccr_id', '{{%client_chat}}', ['cch_ccr_id'], '{{%client_chat_request}}', ['ccr_id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-cch_project_id', '{{%client_chat}}', ['cch_project_id'], '{{%projects}}', ['id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-cch_dep_id', '{{%client_chat}}', ['cch_dep_id'], '{{%department}}', ['dep_id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-cch_client_id', '{{%client_chat}}', ['cch_client_id'], '{{%clients}}', ['id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-cch_owner_user_id', '{{%client_chat}}', ['cch_owner_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-cch_case_id', '{{%client_chat}}', ['cch_case_id'], '{{%cases}}', ['cs_id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-cch_lead_id', '{{%client_chat}}', ['cch_lead_id'], '{{%leads}}', ['id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-cch_language_id', '{{%client_chat}}', ['cch_language_id'], '{{%language}}', ['language_id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-cch_created_user_id', '{{%client_chat}}', ['cch_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-cch_updated_user_id', '{{%client_chat}}', ['cch_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

		$this->createTable('{{%client_chat_channel}}', [
			'ccc_id' => $this->primaryKey(),
			'ccc_name' => $this->string()->notNull()->unique(),
			'ccc_project_id' => $this->integer(),
			'ccc_dep_id' => $this->integer(),
			'ccc_ug_id' => $this->integer(),
			'ccc_disabled' => $this->boolean(),
			'ccc_created_dt' => $this->dateTime(),
			'ccc_updated_dt' => $this->dateTime(),
			'ccc_created_user_id' => $this->integer(),
			'ccc_updated_user_id' => $this->integer()
		], $tableOptions);
		$this->addForeignKey('FK-ccc_project_id', '{{%client_chat_channel}}', ['ccc_project_id'], '{{%projects}}', ['id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-ccc_dep_id', '{{%client_chat_channel}}', ['ccc_dep_id'], '{{%department}}', ['dep_id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-ccc_ug_id', '{{%client_chat_channel}}', ['ccc_ug_id'], '{{%user_group}}', ['ug_id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-ccc_created_user_id', '{{%client_chat_channel}}', ['ccc_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-ccc_updated_user_id', '{{%client_chat_channel}}', ['ccc_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-cch_channel_id', '{{%client_chat}}', ['cch_channel_id'], '{{%client_chat_channel}}', ['ccc_id'], 'SET NULL', 'CASCADE');

		$this->createTable('{{%client_chat_status_log}}', [
			'csl_id' => $this->primaryKey(),
			'csl_cch_id' => $this->integer()->notNull(),
			'csl_from_status' => $this->tinyInteger(),
			'csl_to_status' => $this->tinyInteger(),
			'csl_start_dt' => $this->dateTime(),
			'csl_end_dt' => $this->dateTime(),
			'csl_owner_id' => $this->integer(),
			'csl_description' => $this->string()
		], $tableOptions);
		$this->addForeignKey('FK-csl_cch_id', '{{%client_chat_status_log}}', ['csl_cch_id'], '{{%client_chat}}', 'cch_id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-csl_owner_id', '{{%client_chat_status_log}}', ['csl_owner_id'], '{{%employees}}', 'id', 'SET NULL', 'CASCADE');


		$this->createTable('{{%client_chat_user_channel}}', [
			'ccuc_user_id' => $this->integer()->notNull(),
			'ccuc_channel_id' => $this->integer()->notNull(),
			'ccuc_created_dt' => $this->dateTime(),
			'ccuc_created_user_id' => $this->integer()
		], $tableOptions);
		$this->addPrimaryKey('PK-client_chat_user_channel-user_id-channel_id', '{{%client_chat_user_channel}}', ['ccuc_user_id', 'ccuc_channel_id']);
		$this->addForeignKey('FK-ccuc_user_id', '{{%client_chat_user_channel}}', ['ccuc_user_id'], '{{%employees}}', ['id'], 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-ccuc_channel_id', '{{%client_chat_user_channel}}', ['ccuc_channel_id'], '{{%client_chat_channel}}', ['ccc_id'], 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-ccuc_created_user_id', '{{%client_chat_user_channel}}', ['ccuc_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

		$this->createTable('{{%client_chat_user_access}}', [
			'ccua_cch_id' => $this->integer()->notNull(),
			'ccua_user_id' => $this->integer()->notNull(),
			'ccua_status_id' => $this->tinyInteger(1),
			'ccua_created_dt' => $this->dateTime(),
			'ccua_updated_dt' => $this->dateTime()
		], $tableOptions);
		$this->addPrimaryKey('PK-client_chat_user_access', '{{%client_chat_user_access}}', ['ccua_cch_id', 'ccua_user_id']);
		$this->addForeignKey('FK-ccua_cch_id', '{{%client_chat_user_access}}', ['ccua_cch_id'], '{{%client_chat}}', ['cch_id'], 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-ccua_user_id', '{{%client_chat_user_access}}', ['ccua_user_id'], '{{%employees}}', ['id'], 'CASCADE', 'CASCADE');
		$this->createIndex('IND-ccua_user_id', '{{%client_chat_user_access}}', ['ccua_user_id']);
		$this->createIndex('IND-ccua_status_id', '{{%client_chat_user_access}}', ['ccua_status_id']);

		(new RbacMigrationService())->up($this->routes, $this->roles);
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropForeignKey('FK-ccua_cch_id', '{{%client_chat_user_access}}');
    	$this->dropForeignKey('FK-ccua_user_id', '{{%client_chat_user_access}}');
    	$this->dropTable('{{%client_chat_user_access}}');

    	$this->dropForeignKey('FK-ccuc_user_id', '{{%client_chat_user_channel}}');
    	$this->dropForeignKey('FK-ccuc_channel_id', '{{%client_chat_user_channel}}');
    	$this->dropTable('{{%client_chat_user_channel}}');

		$this->dropForeignKey('FK-csl_cch_id', '{{%client_chat_status_log}}');
		$this->dropForeignKey('FK-csl_owner_id', '{{%client_chat_status_log}}');
		$this->dropTable('{{%client_chat_status_log}}');

		$this->dropForeignKey('FK-ccc_project_id', '{{%client_chat_channel}}');
		$this->dropForeignKey('FK-ccc_dep_id', '{{%client_chat_channel}}');
		$this->dropForeignKey('FK-ccc_ug_id', '{{%client_chat_channel}}');
		$this->dropForeignKey('FK-ccc_created_user_id', '{{%client_chat_channel}}');
		$this->dropForeignKey('FK-ccc_updated_user_id', '{{%client_chat_channel}}');
		$this->dropForeignKey('FK-cch_channel_id', '{{%client_chat}}');
		$this->dropTable('{{%client_chat_channel}}');

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
