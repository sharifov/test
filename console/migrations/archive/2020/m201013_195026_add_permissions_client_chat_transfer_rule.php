<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m201013_195026_add_permissions_client_chat_transfer_rule
 */
class m201013_195026_add_permissions_client_chat_transfer_rule extends Migration
{
    public $route = [
        '/client-chat-channel-transfer/index',
        '/client-chat-channel-transfer/view',
        '/client-chat-channel-transfer/update',
        '/client-chat-channel-transfer/delete',
        '/client-chat-channel-transfer/create',
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
