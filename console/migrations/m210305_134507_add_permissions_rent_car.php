<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210305_134507_add_permissions_rent_car
 */
class m210305_134507_add_permissions_rent_car extends Migration
{
    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    private array $routes = [
        '/rent-car/rent-car-quote/contract-request',
        '/rent-car/rent-car-quote/file-generate',
        '/rent-car/rent-car-quote/book',
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
