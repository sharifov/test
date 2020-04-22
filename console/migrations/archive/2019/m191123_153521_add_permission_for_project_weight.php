<?php

use yii\db\Migration;

/**
 * Class m191123_153521_add_permission_for_project_weight
 */
class m191123_153521_add_permission_for_project_weight extends Migration
{
    public $routes = [
        '/project-weight/*',
    ];

    public $roles = [
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_SUPER_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new \console\migrations\RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new \console\migrations\RbacMigrationService())->down($this->routes, $this->roles);
    }
}
