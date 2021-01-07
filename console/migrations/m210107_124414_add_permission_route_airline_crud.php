<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210107_124414_add_permission_route_airline_crud
 */
class m210107_124414_add_permission_route_airline_crud extends Migration
{
    private $routes = [
        '/airline-crud/index',
        '/airline-crud/view',
        '/airline-crud/create',
        '/airline-crud/update',
        '/airline-crud/delete',
        '/airline-crud/synchronization',
    ];

    private $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
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
