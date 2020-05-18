<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200213_141540_add_permission_for_calculate_user_payroll
 */
class m200213_141540_add_permission_for_calculate_user_payroll extends Migration
{
	public $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
	];

	public $routes = [
		'/user-payroll-crud/calculate-user-payroll',
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
