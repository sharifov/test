<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m201023_135435_add_permission_virtual_cron
 */
class m201023_135435_add_permission_virtual_cron extends Migration
{
    private $route = [
        '/virtual-cron/cron-scheduler/*'
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
