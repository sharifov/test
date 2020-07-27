<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200722_114631_add_permissions_chat_lead_case
 */
class m200722_114631_add_permissions_chat_lead_case extends Migration
{
    public $route = [
        '/client-chat-lead/*',
        '/client-chat-case/*',
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
