<?php
namespace modules\order\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200226_145829_add_permission_for_manage_order_tips_user_profit
 */
class m200226_145829_add_permission_for_manage_order_tips_user_profit extends Migration
{
	public $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
	];

	public $routes = [
		'/order/order-tips-user-profit/ajax-manage-order-tips-user-profit',
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
