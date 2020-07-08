<?php

use yii\db\Migration;
use common\models\Employee;

/**
 * Class m200708_084159_add_permission_client_chat_report
 */
class m200708_084159_add_permission_client_chat_report extends Migration
{
    public $route = [
        '/client-chat/report',
        '/client-chat/stats'
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
