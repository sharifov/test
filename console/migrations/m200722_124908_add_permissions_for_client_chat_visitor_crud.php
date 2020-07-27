<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200722_124908_add_permissions_for_client_chat_visitor_crud
 */
class m200722_124908_add_permissions_for_client_chat_visitor_crud extends Migration
{
	public $route = [
		'/client-chat-visitor-crud/index',
		'/client-chat-visitor-crud/create',
		'/client-chat-visitor-crud/view',
		'/client-chat-visitor-crud/update',
		'/client-chat-visitor-crud/delete',

		'/client-chat-visitor-data-crud/index',
		'/client-chat-visitor-data-crud/create',
		'/client-chat-visitor-data-crud/view',
		'/client-chat-visitor-data-crud/update',
		'/client-chat-visitor-data-crud/delete',
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
