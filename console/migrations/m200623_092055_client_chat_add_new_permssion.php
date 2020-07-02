<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200623_092055_client_chat_add_new_permssion
 */
class m200623_092055_client_chat_add_new_permssion extends Migration
{
	public $routes = [
		'/client-chat/info',
	];

	public $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_AGENT,
		Employee::ROLE_SUPERVISION,
		Employee::ROLE_SUP_AGENT,
		Employee::ROLE_EX_AGENT,
		Employee::ROLE_EX_SUPER,
		Employee::ROLE_SALES_SENIOR,
		Employee::ROLE_EXCHANGE_SENIOR,
		Employee::ROLE_SUP_SUPER,
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
		(new RbacMigrationService())->down($this->routes, $this->roles);
	}
}
