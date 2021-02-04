<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200923_143251_add_permissions_for_client_chat_stats
 */
class m200923_143251_add_permissions_for_client_chat_stats extends Migration
{
    public $route = [
        '/client-chat/stats',
        '/client-chat/ajax-get-chart-stats',
        '/client-chat/extended-stats',
        '/client-chat/ajax-get-extended-stats-chart'
    ];

    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
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
