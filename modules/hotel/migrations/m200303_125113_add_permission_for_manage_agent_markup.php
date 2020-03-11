<?php
namespace modules\hotel\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200303_125113_add_permission_for_manage_agent_markup
 */
class m200303_125113_add_permission_for_manage_agent_markup extends Migration
{
	public $routes = [
		'/hotel/hotel-quote/ajax-update-agent-markup',
	];

	public $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
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
