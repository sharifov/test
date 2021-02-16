<?php

namespace modules\qaTask\migrations;

use yii\db\Migration;

/**
 * Class m210216_092341_add_permission_to_create_rule
 */
class m210216_092341_add_permission_to_create_rule extends Migration
{
    public $routes = [
        '/qa-task-rules/create',
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
