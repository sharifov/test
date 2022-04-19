<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m211217_120116_add_rbac_access_to_tools_db_view
 */
class m211217_120116_add_rbac_access_to_tools_db_view extends Migration
{
    public $routes = [
        '/tools/db-view',
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
