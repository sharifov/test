<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200810_073051_add_permission_to_close_chat_for_all_roles
 */
class m200810_073051_add_permission_to_close_chat_for_all_roles extends Migration
{
	public $routes = [
		'/client-chat/ajax-close',
	];

	public $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
		Employee::ROLE_AGENT,
		Employee::ROLE_SUPERVISION,
		Employee::ROLE_SUP_AGENT,
		Employee::ROLE_EX_AGENT,
		Employee::ROLE_EX_SUPER,
		Employee::ROLE_SALES_SENIOR,
		Employee::ROLE_EXCHANGE_SENIOR,
		Employee::ROLE_SUP_SUPER,
		Employee::ROLE_QA,
		Employee::ROLE_QA_SUPER,
		Employee::ROLE_SUPPORT_SENIOR,
		Employee::ROLE_USER_MANAGER,
	];
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		(new RbacMigrationService())->up($this->routes, $this->roles);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		(new RbacMigrationService())->up($this->routes, $this->roles);
	}
}
