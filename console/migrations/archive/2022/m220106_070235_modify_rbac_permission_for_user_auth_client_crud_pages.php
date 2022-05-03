<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220106_070235_modify_rbac_permission_for_user_auth_client_crud_pages
 */
class m220106_070235_modify_rbac_permission_for_user_auth_client_crud_pages extends Migration
{
    private array $oldRoutes = [
        '/auth-client-crud/index',
        '/auth-client-crud/view',
        '/auth-client-crud/delete',
        '/auth-client-crud/create',
        '/auth-client-crud/update',
    ];

    private array $newRoutes = [
        '/user-auth-client-crud/index',
        '/user-auth-client-crud/view',
        '/user-auth-client-crud/delete',
        '/user-auth-client-crud/create',
        '/user-auth-client-crud/update',
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
        (new RbacMigrationService())->down($this->oldRoutes, $this->roles);
        (new RbacMigrationService())->up($this->newRoutes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->newRoutes, $this->roles);
        (new RbacMigrationService())->up($this->oldRoutes, $this->roles);
    }
}
