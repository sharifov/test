<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220126_145214_add_rbac_permissions_for_user_feedback_stats_page
 */
class m220126_145214_add_rbac_permissions_for_user_feedback_stats_page extends Migration
{
    private array $route = [
        '/stats/user-feedback',
        '/stats/ajax-get-user-feedback-chart'
    ];

    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN
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
