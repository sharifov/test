<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m211205_141009_add_permissions_phone_device_log
 */
class m211205_141009_add_permissions_phone_device_log extends Migration
{
    private array $routes = [
        'crud' => [
            '/phone-device-log/*',
            '/phone-device-crud/*',
        ],
        '/phone-device-crud/invalidate-cache-token',
    ];

    private array $roles = [
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
