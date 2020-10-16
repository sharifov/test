<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m201016_052307_add_permission_for_chat_multiple_update
 */
class m201016_052307_add_permission_for_chat_multiple_update extends Migration
{
    private $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN
    ];

    private $routes = [
        '/client-chat/ajax-multiple-update'
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
