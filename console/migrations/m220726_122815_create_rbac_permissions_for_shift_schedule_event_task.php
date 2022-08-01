<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220726_122815_create_rbac_permissions_for_shift_schedule_event_task
 */
class m220726_122815_create_rbac_permissions_for_shift_schedule_event_task extends Migration
{
    private array $routes = [
        '/task/shift-schedule-event-task-crud/index',
        '/task/shift-schedule-event-task-crud/view',
        '/task/shift-schedule-event-task-crud/create',
        '/task/shift-schedule-event-task-crud/update',
        '/task/shift-schedule-event-task-crud/delete',
        '/object-segment/object-segment-task-crud/index',
        '/object-segment/object-segment-task-crud/view',
        '/object-segment/object-segment-task-crud/create',
        '/object-segment/object-segment-task-crud/update',
        '/object-segment/object-segment-task-crud/delete',
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
