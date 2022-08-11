<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220712_081257_create_rbac_permissions_for_client_user_return_crud_pages
 */
class m220712_081257_create_rbac_permissions_for_client_user_return_crud_pages extends Migration
{
    private array $routes = [
        '/client-user-return-crud/index',
        '/client-user-return-crud/view',
        '/client-user-return-crud/create',
        '/client-user-return-crud/update',
        '/client-user-return-crud/delete',
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
