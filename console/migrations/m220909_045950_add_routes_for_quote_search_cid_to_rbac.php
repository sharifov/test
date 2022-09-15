<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220909_045950_add_routes_for_quote_search_cid_to_rbac
 */
class m220909_045950_add_routes_for_quote_search_cid_to_rbac extends Migration
{
    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_QA,
        Employee::ROLE_DEV,
    ];

    private array $routes = [
        '/quote-search-cid/index',
        '/quote-search-cid/create',
        '/quote-search-cid/update',
        '/quote-search-cid/view',
        '/quote-search-cid/delete',
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
