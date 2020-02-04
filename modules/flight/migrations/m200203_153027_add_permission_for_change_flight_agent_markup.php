<?php
namespace modules\flight\migrations;

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200203_153027_add_permission_for_change_flight_agent_markup
 */
class m200203_153027_add_permission_for_change_flight_agent_markup extends Migration
{
	public $routes = [
		'/flight/flight-quote/ajax-update-agent-markup',
	];

	public $roles = [
		\common\models\Employee::ROLE_ADMIN,
		\common\models\Employee::ROLE_SUPER_ADMIN,
//		\common\models\Employee::ROLE_AGENT,
//		\common\models\Employee::ROLE_EX_AGENT,
//		\common\models\Employee::ROLE_EX_SUPER,
//		\common\models\Employee::ROLE_SUP_AGENT,
//		\common\models\Employee::ROLE_SUP_SUPER,
//		\common\models\Employee::ROLE_SUPERVISION,
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
