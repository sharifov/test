<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m201028_114349_add_permission_flush_redis_reservation
 */
class m201028_114349_add_permission_flush_redis_reservation extends Migration
{
    private $route = [
        '/lead-qcall/flush'
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
