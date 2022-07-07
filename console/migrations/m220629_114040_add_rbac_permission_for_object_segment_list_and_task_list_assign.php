<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220629_114040_add_rbac_permission_for_object_segment_list_and_task_list_assign
 */
class m220629_114040_add_rbac_permission_for_object_segment_list_and_task_list_assign extends Migration
{
    private array $routes = [
        '/object-segment/object-segment-list/assign-form',
        '/object-segment/object-segment-list/assign-validation',
        '/object-segment/object-segment-list/assign',

        '/task/task-list/assign-form',
        '/task/task-list/assign-validation',
        '/task/task-list/assign',
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
