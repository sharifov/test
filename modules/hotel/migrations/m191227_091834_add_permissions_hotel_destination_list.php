<?php
namespace modules\hotel\migrations;

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m191227_091834_add_permissions_hotel_destination_list
 */
class m191227_091834_add_permissions_hotel_destination_list extends Migration
{
	public $routes = [
		'/hotel/hotel/ajax-get-destination-list',
	];

	public $roles = [
		\common\models\Employee::ROLE_ADMIN,
		\common\models\Employee::ROLE_SUPER_ADMIN,
		\common\models\Employee::ROLE_AGENT,
		\common\models\Employee::ROLE_EX_AGENT,
		\common\models\Employee::ROLE_EX_SUPER,
		\common\models\Employee::ROLE_SUP_AGENT,
		\common\models\Employee::ROLE_SUP_SUPER,
		\common\models\Employee::ROLE_SUPERVISION,
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
