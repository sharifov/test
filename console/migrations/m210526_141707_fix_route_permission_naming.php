<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210526_141707_fix_route_permission_naming
 */
class m210526_141707_fix_route_permission_naming extends Migration
{
    private $oldRoutes = [
        '/lead-request-crud/updated',
        '/lead-data-crud/updated',
        '/app-project-key-crud/updated',
        '/project-relation-crud/updated',
        '/client-visitor-crud/updated',
    ];

    private $newRoutes = [
        '/lead-request-crud/update',
        '/lead-data-crud/update',
        '/app-project-key-crud/update',
        '/project-relation-crud/update',
        '/client-visitor-crud/update',
    ];

    private $roles = [
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
        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->up($this->oldRoutes, $this->roles);
        (new RbacMigrationService())->down($this->newRoutes, $this->roles);
        Yii::$app->cache->flush();
    }
}
