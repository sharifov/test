<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200709_111956_add_permission_for_update_chat_widget
 */
class m200709_111956_add_permission_for_update_chat_widget extends Migration
{
	public $route = [
		'/client-chat/pjax-update-chat-widget',
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
		(new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		(new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
	}
}
