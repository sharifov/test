<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m201030_065117_fix_cyrillic_symbol
 */
class m201030_065117_fix_cyrillic_symbol extends Migration
{
    public $route = [
        'client-chat/dashboard/filter/channel',
        'client-chat/dashboard/filter/status',
        'client-chat/dashboard/filter/user',
        'client-chat/dashboard/filter/created_date',
        'client-chat/dashboard/filter/department',
        'client-chat/dashboard/filter/project',
        'client-chat/dashboard/filter/read_unread',
        'client-chat/dashboard/filter/group/my_chats',
        'client-chat/dashboard/filter/group/other_chats',
        'client-chat/dashboard/filter/group/free_to_take_chats',
    ];

    public $routeWithCyrillic = [
        'client-сhat/dashboard/filter/channel',
        'client-сhat/dashboard/filter/status',
        'client-сhat/dashboard/filter/user',
        'client-сhat/dashboard/filter/created_date',
        'client-сhat/dashboard/filter/department',
        'client-сhat/dashboard/filter/project',
        'client-сhat/dashboard/filter/read_unread',
        'client-сhat/dashboard/filter/group/my_chats',
        'client-сhat/dashboard/filter/group/other_chats',
        'client-сhat/dashboard/filter/group/free_to_take_chats',
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
        (new RbacMigrationService())->down($this->routeWithCyrillic, $this->roles);
        (new RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->route, $this->roles);
        (new RbacMigrationService())->up($this->routeWithCyrillic, $this->roles);
    }
}
