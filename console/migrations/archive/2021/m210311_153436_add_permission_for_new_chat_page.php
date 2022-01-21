<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m210311_153436_add_permission_for_new_chat_page
 */
class m210311_153436_add_permission_for_new_chat_page extends Migration
{
    private array $route = [
        '/client-chat/dashboard-v2'
    ];

    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN
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
