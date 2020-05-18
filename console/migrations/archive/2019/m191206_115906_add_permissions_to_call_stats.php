<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m191206_115906_add_permissions_to_call_stats
 */
class m191206_115906_add_permissions_to_call_stats extends Migration
{

	public $routes = [
		'/stats/ajax-get-total-chart',
	];

	public $roles = [
		\common\models\Employee::ROLE_ADMIN,
		\common\models\Employee::ROLE_SUPER_ADMIN,
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
