<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m201002_091040_create_tbl_client_chat_status_log_reason
 */
class m201002_091040_create_tbl_client_chat_status_log_reason extends Migration
{
	public $route = [
		'/client-chat-status-log-reason-crud/index',
		'/client-chat-status-log-reason-crud/view',
		'/client-chat-status-log-reason-crud/create',
		'/client-chat-status-log-reason-crud/update',
		'/client-chat-status-log-reason-crud/delete',
	];

	public $role = [
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

		$tblName = '{{%client_chat_status_log_reason}}';

		$this->createTable($tblName, [
			'cslr_id' => $this->primaryKey(),
			'cslr_status_log_id' => $this->integer(),
			'cslr_action_reason_id' => $this->integer(),
			'cslr_comment' => $this->string(100)
		], $tableOptions);

		$this->addForeignKey('FK-cslr_status_log_id', $tblName, ['cslr_status_log_id'], 'client_chat_status_log', 'csl_id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-cslr_action_reason_id', $tblName, ['cslr_action_reason_id'], 'client_chat_action_reason', 'ccar_id', 'CASCADE', 'CASCADE');

		(new \console\migrations\RbacMigrationService())->up($this->route, $this->role);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->dropTable('{{%client_chat_status_log_reason}}');

		(new \console\migrations\RbacMigrationService())->down($this->route, $this->role);
	}
}
