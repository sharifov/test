<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200723_113802_permission_site_env
 */
class m200723_113802_permission_site_env extends Migration
{
    public $route = [
        '/setting/env',
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
