<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200513_212641_add_permission_for_accept_incoming_call
 */
class m200513_212641_add_permission_for_accept_incoming_call extends Migration
{
	public $route = [
		'/call/ajax-accept-incoming-call',
	];

	public $roles = [
		Employee::ROLE_SUPER_ADMIN,
		Employee::ROLE_ADMIN,
	];
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		(new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		(new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
	}
}
