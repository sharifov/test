<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200113_095446_add_permissions_case_multi_update
 */
class m200113_095446_add_permissions_case_multi_update extends Migration
{
    public $routes = [
        '/cases-multiple-update/show',
        '/cases-multiple-update/validation',
        '/cases-multiple-update/update',
    ];

    public $roles = [
        \common\models\Employee::ROLE_SUPER_ADMIN,
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_EX_SUPER,
        \common\models\Employee::ROLE_SUP_SUPER,
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
