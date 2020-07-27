<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200707_075345_add_permission_for_close_chat
 */
class m200707_075345_add_permission_for_close_chat extends Migration
{
	private array $routes = [
		'/client-chat/ajax-close',
	];

	private array $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
	];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		(new \console\migrations\RbacMigrationService())->up($this->routes, $this->roles);
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		(new \console\migrations\RbacMigrationService())->down($this->routes, $this->roles);
    }
}
