<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220318_093636_add_rbac_permission_to_feature_flag
 */
class m220318_093636_add_rbac_permission_to_feature_flag extends Migration
{
    private $routes = [
        '/flag/feature-flag/index',
        '/flag/feature-flag/view',
        '/flag/feature-flag/update',
        '/flag/feature-flag/clear-cache',
        '/flag/feature-flag/doc',
        '/flag/feature-flag/clear-doc-cache',
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
