<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200212_082121_add_permission_for_user_profit_crud
 */
class m200212_082121_add_permission_for_user_profit_crud extends Migration
{
	public $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
	];

	public $routes = [
		'/user-profit-crud/index',
		'/user-profit-crud/create',
		'/user-profit-crud/update',
		'/user-profit-crud/delete',
		'/user-profit-crud/view',
	];

	public function safeUp()
	{
		(new RbacMigrationService())->up($this->routes, $this->roles);
	}

	public function safeDown()
	{
		(new RbacMigrationService())->down($this->routes, $this->roles);
	}
}
