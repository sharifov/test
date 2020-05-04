<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200504_123732_update_permission_for_wg_call_history
 */
class m200504_123732_update_permission_for_wg_call_history extends Migration
{
	public $routesDisable = [
		'/call/ajax-get-call-history',
	];

	public $routes = [
		'/call-log/ajax-get-call-history'
	];

	public $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
		Employee::ROLE_AGENT,
		Employee::ROLE_SUPERVISION,
		Employee::ROLE_QA,
		Employee::ROLE_QA_SUPER,
		Employee::ROLE_USER_MANAGER,
		Employee::ROLE_SUP_AGENT,
		Employee::ROLE_EX_AGENT,
		Employee::ROLE_EX_SUPER,
		Employee::ROLE_SALES_SENIOR,
	];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		(new RbacMigrationService())->down($this->routesDisable, $this->roles);
		(new RbacMigrationService())->up($this->routes, $this->roles);

		if (\Yii::$app->cache) {
			\Yii::$app->cache->flush();
		}
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		(new RbacMigrationService())->up($this->routesDisable, $this->roles);
		(new RbacMigrationService())->down($this->routes, $this->roles);

		if (\Yii::$app->cache) {
			\Yii::$app->cache->flush();
		}
    }
}
