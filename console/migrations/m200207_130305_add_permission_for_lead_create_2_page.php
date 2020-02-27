<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200207_130305_add_permission_for_lead_create_2_page
 */
class m200207_130305_add_permission_for_lead_create_2_page extends Migration
{
	public $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
	];

	public $routes = [
		'/lead/create2'
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
