<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m201005_134928_add_permissions_active_chat_connection
 */
class m201005_134928_add_permissions_active_chat_connection extends Migration
{
    public $route = [
        '/user-connection-active-chat/index',
        '/user-connection-active-chat/view',
        '/user-connection-active-chat/update',
        '/user-connection-active-chat/create',
        '/user-connection-active-chat/delete',

        '/client-chat/add-active-connection',
        '/client-chat/remove-active-connection',
    ];

    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
        Employee::ROLE_AGENT,
        Employee::ROLE_SUPERVISION,
        Employee::ROLE_QA,
        Employee::ROLE_QA_SUPER,
        Employee::ROLE_USER_MANAGER,
        Employee::ROLE_SUP_AGENT,
        Employee::ROLE_SUP_SUPER,
        Employee::ROLE_EX_AGENT,
        Employee::ROLE_EX_SUPER,
        Employee::ROLE_SALES_SENIOR,
        Employee::ROLE_EXCHANGE_SENIOR,
        Employee::ROLE_SUPPORT_SENIOR,
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
