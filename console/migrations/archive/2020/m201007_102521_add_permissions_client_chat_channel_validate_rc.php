<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m201007_102521_add_permissions_client_chat_channel_validate_rc
 */
class m201007_102521_add_permissions_client_chat_channel_validate_rc extends Migration
{
    public $route = [
        '/client-chat-channel-crud/validate',
        '/client-chat-channel-crud/validate-all',
        '/client-chat-channel-crud/register',
        '/client-chat-channel-crud/register-all',
        '/client-chat-channel-crud/un-register',
        '/client-chat-channel-crud/un-register-all',
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
