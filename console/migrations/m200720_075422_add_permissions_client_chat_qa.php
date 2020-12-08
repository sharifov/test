<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200720_075422_add_permissions_client_chat_qa
 */
class m200720_075422_add_permissions_client_chat_qa extends Migration
{
    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_QA,
        Employee::ROLE_QA_SUPER,
    ];

    private array $routes = [
        '/client-chat-qa/index',
        '/client-chat-qa/room',
        '/client-chat-qa/view',
        '/client-chat-qa/message',
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
