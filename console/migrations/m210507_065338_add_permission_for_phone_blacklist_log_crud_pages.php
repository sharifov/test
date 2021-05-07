<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m210507_065338_add_permission_for_phone_blacklist_log_crud_pages
 */
class m210507_065338_add_permission_for_phone_blacklist_log_crud_pages extends Migration
{
    private array $routes = [
        '/phone-blacklist-log-crud/index',
        '/phone-blacklist-log-crud/create',
        '/phone-blacklist-log-crud/update',
        '/phone-blacklist-log-crud/delete',
        '/phone-blacklist-log-crud/view',
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
