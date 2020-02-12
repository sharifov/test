<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200211_110032_add_permission_for_user_payment_category_crud
 */
class m200211_110032_add_permission_for_user_payment_category_crud extends Migration
{
	public $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
	];

	public $routes = [
		'/user-payment-category-crud/index',
		'/user-payment-category-crud/create',
		'/user-payment-category-crud/update',
		'/user-payment-category-crud/delete',
		'/user-payment-category-crud/view',
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
