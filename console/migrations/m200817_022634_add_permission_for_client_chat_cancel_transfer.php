<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200817_022634_add_permission_for_client_chat_cancel_transfer
 */
class m200817_022634_add_permission_for_client_chat_cancel_transfer extends Migration
{
	public $route = [
		'/client-chat/ajax-cancel-transfer',
	];

	public $roles = [
		Employee::ROLE_SUPER_ADMIN,
		Employee::ROLE_ADMIN,
		Employee::ROLE_AGENT,
		Employee::ROLE_SUPERVISION,
		Employee::ROLE_QA,
		Employee::ROLE_QA_SUPER,
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
		(new RbacMigrationService())->up($this->route, $this->roles);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		(new RbacMigrationService())->down($this->route, $this->roles);
	}
}
