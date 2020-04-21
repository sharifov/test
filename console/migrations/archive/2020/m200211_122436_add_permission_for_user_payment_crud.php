<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200211_122436_add_permission_for_user_payment_crud
 */
class m200211_122436_add_permission_for_user_payment_crud extends Migration
{
	public $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
	];

	public $routes = [
		'/user-payment-crud/index',
		'/user-payment-crud/create',
		'/user-payment-crud/update',
		'/user-payment-crud/delete',
		'/user-payment-crud/view',
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
