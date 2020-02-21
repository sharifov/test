<?php
namespace modules\order\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200217_074415_add_permissions_for_order_user_profit_crud_pages
 */
class m200217_074415_add_permissions_for_order_user_profit_crud_pages extends Migration
{
	public $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
	];

	public $routes = [
		'/order/order-user-profit-crud/index',
		'/order/order-user-profit-crud/create',
		'/order/order-user-profit-crud/update',
		'/order/order-user-profit-crud/delete',
		'/order/order-user-profit-crud/view',
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
