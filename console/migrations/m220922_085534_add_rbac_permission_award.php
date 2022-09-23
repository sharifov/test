<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220922_085534_add_rbac_permission_award
 */
class m220922_085534_add_rbac_permission_award extends Migration
{
    private array $routes = [
        '/quote-award/create',
        '/quote-award/update',
        '/quote-award/calc-price',
        '/quote-award/save',
        '/quote-award/import-gds-dump',
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
