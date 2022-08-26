<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220825_061451_add_rbac_permission_for_multiple_delete_user_tasks
 */
class m220825_061451_add_rbac_permission_for_multiple_delete_user_tasks extends Migration
{
    private array $routes = [
        '/task/user-task-crud/multiple-delete',
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
