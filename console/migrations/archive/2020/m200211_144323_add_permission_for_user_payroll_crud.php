<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200211_144323_add_permission_for_user_payroll_crud
 */
class m200211_144323_add_permission_for_user_payroll_crud extends Migration
{
	public $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
	];

	public $routes = [
		'/user-payroll-crud/index',
		'/user-payroll-crud/create',
		'/user-payroll-crud/update',
		'/user-payroll-crud/delete',
		'/user-payroll-crud/view',
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
