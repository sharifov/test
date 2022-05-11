<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220503_065107_add_rbac_permission_for_object_segment_module
 */
class m220503_065107_add_rbac_permission_for_object_segment_module extends Migration
{
    private array $routes = [
        '/object-segment/object-segment-list/index',
        '/object-segment/object-segment-list/create',
        '/object-segment/object-segment-list/update',
        '/object-segment/object-segment-list/delete',
        '/object-segment/object-segment-list/view',
        '/object-segment/object-segment-list/invalidate-cache',

        '/object-segment/object-segment-rule/index',
        '/object-segment/object-segment-rule/create',
        '/object-segment/object-segment-rule/update',
        '/object-segment/object-segment-rule/delete',
        '/object-segment/object-segment-rule/view',
        '/object-segment/object-segment-rule/invalidate-cache',
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
