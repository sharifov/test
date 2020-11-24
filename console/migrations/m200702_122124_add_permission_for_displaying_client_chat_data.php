<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200702_122124_add_permission_for_displaying_client_chat_data
 */
class m200702_122124_add_permission_for_displaying_client_chat_data extends Migration
{
    private array $routes = [
        '/client-chat/ajax-data-info',
    ];

    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new \console\migrations\RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new \console\migrations\RbacMigrationService())->down($this->routes, $this->roles);
    }
}
