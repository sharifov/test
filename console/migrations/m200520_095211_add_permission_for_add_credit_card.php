<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200520_095211_add_permission_for_add_credit_card
 */
class m200520_095211_add_permission_for_add_credit_card extends Migration
{
	public $route = [
		'/credit-card/ajax-add-credit-card',
	];

	public $roles = [
		Employee::ROLE_SUPER_ADMIN,
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUP_AGENT,
		Employee::ROLE_SUP_SUPER,
		Employee::ROLE_EX_AGENT,
		Employee::ROLE_EX_SUPER,
		Employee::ROLE_EXCHANGE_SENIOR,
		Employee::ROLE_SUPPORT_SENIOR,
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
