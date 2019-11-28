<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m191128_083835_add_permissions_for_client_email
 */
class m191128_083835_add_permissions_for_client_email extends Migration
{
    public $routes = [
        '/client-email/*',
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
