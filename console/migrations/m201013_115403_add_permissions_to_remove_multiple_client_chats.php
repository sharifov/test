<?php

use yii\db\Migration;
use common\models\Employee;
Use console\migrations\RbacMigrationService;

/**
 * Class m201013_115403_add_permissions_to_remove_multiple_client_chats
 */
class m201013_115403_add_permissions_to_remove_multiple_client_chats extends Migration
{
    public $route = [
        '/client-chat-crud/select-all',
        '/client-chat-crud/delete-selected',
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
        (new RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->route, $this->roles);
    }
}
