<?php

use yii\db\Migration;

/**
 * Class m200211_132056_create_rbac_permission_tools_check_dump
 */
class m200211_132056_create_rbac_permission_tools_check_dump extends Migration
{
    public $roles = [
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        '/tools/check-flight-dump',
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
