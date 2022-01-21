<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210614_145044_add_permissions_for_visitor_subscription_crud_pages
 */
class m210614_145044_add_permissions_for_visitor_subscription_crud_pages extends Migration
{
    private array $routes = [
        '/visitor-subscription-crud/index',
        '/visitor-subscription-crud/create',
        '/visitor-subscription-crud/view',
        '/visitor-subscription-crud/update',
        '/visitor-subscription-crud/delete',
    ];

    private array $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN
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
