<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m201021_085206_remove_route_permission_for_reopen_closed_chat
 */
class m201021_085206_remove_route_permission_for_reopen_closed_chat extends Migration
{
    private $route = [
        '/client-chat/ajax-reopen-chat'
    ];

    private $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
    }
}
