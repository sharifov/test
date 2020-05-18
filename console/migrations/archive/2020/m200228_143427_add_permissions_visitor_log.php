<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200228_143427_add_permissions_visitor_log
 */
class m200228_143427_add_permissions_visitor_log extends Migration
{
    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        'crud' => [
            '/visitor-log/*',
        ]
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
