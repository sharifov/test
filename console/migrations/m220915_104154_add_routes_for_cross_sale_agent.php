<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220915_104154_add_routes_for_cross_sale_agent
 */
class m220915_104154_add_routes_for_cross_sale_agent extends Migration
{
    private array $roles = [
        Employee::ROLE_CROSS_SALE_AGENT,
    ];

    private array $routes = [
        '/cases-q/cross-sale-inbox',
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
