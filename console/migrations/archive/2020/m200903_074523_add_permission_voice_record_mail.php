<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200903_074523_add_permission_voice_record_mail
 */
class m200903_074523_add_permission_voice_record_mail extends Migration
{
    public $route = [
        '/voice-mail-record/remove',
        '/voice-mail-record/showed',
        '/voice-mail-record/list',
        '/voice-mail-record/count',
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

    public $routeCrud = [
        '/voice-mail-record/index',
        '/voice-mail-record/create',
        '/voice-mail-record/update',
        '/voice-mail-record/delete',
        '/voice-mail-record/view',
    ];

    public $rolesAdmin = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new RbacMigrationService())->up($this->route, $this->roles);
        (new RbacMigrationService())->up($this->routeCrud, $this->rolesAdmin);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->route, $this->roles);
        (new RbacMigrationService())->down($this->routeCrud, $this->rolesAdmin);
    }
}
