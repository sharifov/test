<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m201013_080533_add_permission_to_refresh_token_rc
 */
class m201013_080533_add_permission_to_refresh_token_rc extends Migration
{
    private $route = [
        '/employee/refresh-rocket-chat-user-token',
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
