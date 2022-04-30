<?php

use yii\db\Migration;
use common\models\Employee;
use console\migrations\RbacMigrationService;

/**
 * Class m220430_154341_add_rbac_permissions_for_client_chat_survey_response_table
 */
class m220430_154341_add_rbac_permissions_for_client_chat_survey_response_table extends Migration
{
    private $routes = [
        '/client-chat-survey-response/index',
        '/client-chat-survey-response/create',
        '/client-chat-survey-response/update',
        '/client-chat-survey-response/delete',
        '/client-chat-survey-response/view',
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
