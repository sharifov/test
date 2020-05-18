<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200116_150124_add_permission_refresh_case_sale_info
 */
class m200116_150124_add_permission_refresh_case_sale_info extends Migration
{
	public $routes = [
		'/cases/ajax-refresh-sale-info',
	];

	public $roles = [
		\common\models\Employee::ROLE_SUPER_ADMIN,
		\common\models\Employee::ROLE_ADMIN,
//		\common\models\Employee::ROLE_EXCHANGE_SENIOR,
//		\common\models\Employee::ROLE_SALES_SENIOR,
//		\common\models\Employee::ROLE_SUPPORT_SENIOR,
	];

	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		(new RbacMigrationService())->up($this->routes, $this->roles);

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		(new RbacMigrationService())->down($this->routes, $this->roles);

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
	}
}
