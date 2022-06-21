<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220620_114941_add_permission_for_profit_split
 */
class m220620_114941_add_permission_for_profit_split extends Migration
{
    private array $routes = [
        '/profit-split-crud/create',
        '/profit-split-crud/update',
        '/profit-split-crud/view',
        '/profit-split-crud/delete',
        '/profit-split-crud/index',
        '/profit-split-crud/sold-lead-list',
    ];

    private array $roles = [
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
