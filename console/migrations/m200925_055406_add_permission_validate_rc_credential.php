<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200925_055406_add_permission_validate_rc_credential
 */
class m200925_055406_add_permission_validate_rc_credential extends Migration
{
    public $route = [
        '/employee/validate-rocket-chat-credential',
    ];

    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPERVISION,
        Employee::ROLE_EX_SUPER,
        Employee::ROLE_SUP_SUPER,
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
