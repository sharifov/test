<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m201127_123710_add_permission_to_client_account_social
 */
class m201127_123710_add_permission_to_client_account_social extends Migration
{
    private $routes = [
        '/client-account-social-crud/view',
        '/client-account-social-crud/index',
        '/client-account-social-crud/create',
        '/client-account-social-crud/update',
        '/client-account-social-crud/delete',
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
