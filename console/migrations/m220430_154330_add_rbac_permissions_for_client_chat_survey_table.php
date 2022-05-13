<?php

use yii\db\Migration;
use common\models\Employee;
use console\migrations\RbacMigrationService;

/**
 * Class m220430_154330_add_rbac_permissions_for_client_chat_survey_table
 */
class m220430_154330_add_rbac_permissions_for_client_chat_survey_table extends Migration
{
    private $routes = [
        '/client-chat-survey/index',
        '/client-chat-survey/create',
        '/client-chat-survey/update',
        '/client-chat-survey/delete',
        '/client-chat-survey/view',
    ];

    private $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN
    ];

    /**
     * @return false|mixed|void
     * @throws \yii\base\Exception
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
