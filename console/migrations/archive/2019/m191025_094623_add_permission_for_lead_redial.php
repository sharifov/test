<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m191025_094623_add_permission_for_lead_redial
 */
class m191025_094623_add_permission_for_lead_redial extends Migration
{

    public $routes = [
        '/lead-redial/reservation',
    ];

    public $roles = [
        'admin'//, 'agent', 'supervision', 'ex_agent', 'ex_super', 'qa'
    ];

    public function safeUp()
    {
        (new RbacMigrationService())->up($this->routes, $this->roles);

    }

    public function safeDown()
    {
        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
