<?php

use yii\db\Migration;
use common\models\Employee;
use console\migrations\RbacMigrationService;

/**
 * Class m201002_092342_add_permissions_for_client_chat_feedback_stats
 */
class m201002_092342_add_permissions_for_client_chat_feedback_stats extends Migration
{
    public $route = [
        '/client-chat/feedback-stats',
        '/client-chat/ajax-get-feedback-stats-chart',
    ];

    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPERVISION,
        Employee::ROLE_QA,
        Employee::ROLE_QA_SUPER,
        Employee::ROLE_SUP_SUPER,
        Employee::ROLE_EX_SUPER
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
