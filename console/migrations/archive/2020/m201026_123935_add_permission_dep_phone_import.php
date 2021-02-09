<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m201026_123935_add_permission_dep_phone_import
 */
class m201026_123935_add_permission_dep_phone_import extends Migration
{
    private $route = [
        '/department-phone-project/import'
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
        (new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
    }
}
