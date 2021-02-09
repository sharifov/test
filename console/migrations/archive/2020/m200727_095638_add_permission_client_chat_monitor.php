<?php

use yii\db\Migration;
use common\models\Employee;

/**
 * Class m200727_095638_add_permission_client_chat_monitor
 */
class m200727_095638_add_permission_client_chat_monitor extends Migration
{
    public $route = [
        '/client-chat/monitor'
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
