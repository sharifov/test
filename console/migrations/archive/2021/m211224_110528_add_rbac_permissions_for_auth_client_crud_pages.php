<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m211224_110528_add_rbac_permissions_for_auth_client_crud_pages
 */
class m211224_110528_add_rbac_permissions_for_auth_client_crud_pages extends Migration
{
    private array $routes = [
        '/auth-client-crud/index',
        '/auth-client-crud/create',
        '/auth-client-crud/update',
        '/auth-client-crud/delete',
        '/auth-client-crud/view',
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
