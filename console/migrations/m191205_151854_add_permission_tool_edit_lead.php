<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m191205_151854_add_permission_tool_edit_lead
 */
class m191205_151854_add_permission_tool_edit_lead extends Migration
{
    public $routes = [
        '/leads/edit',
        '/leads/edit-validation',
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
