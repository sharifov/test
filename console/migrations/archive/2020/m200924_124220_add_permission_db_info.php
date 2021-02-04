<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200924_124220_add_permission_db_info
 */
class m200924_124220_add_permission_db_info extends Migration
{
    public $route = [
        '/tools/db-info',
    ];

    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->route, $this->roles);
    }
}
