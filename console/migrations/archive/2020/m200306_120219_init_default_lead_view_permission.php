<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200306_120219_init_default_lead_view_permission
 */
class m200306_120219_init_default_lead_view_permission extends Migration
{
    public $adminRoles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
        Employee::ROLE_EX_SUPER,
        Employee::ROLE_EXCHANGE_SENIOR,
        Employee::ROLE_QA,
        Employee::ROLE_QA_SUPER,
        Employee::ROLE_SALES_SENIOR,
        Employee::ROLE_SUP_SUPER,
        Employee::ROLE_SUPERVISION,
        Employee::ROLE_SUPPORT_SENIOR,
        Employee::ROLE_SUP_AGENT,
    ];

    public $adminPermissions = ['lead/view'];

    public $simpleRoles = [
        Employee::ROLE_AGENT,
        Employee::ROLE_EX_AGENT,
    ];

    public $simplePermissions = [
        'lead/view_Processing',
        'lead/view_Reject',
        'lead/view_FollowUp',
        'lead/view_Sold',
        'lead/view_Booked',
        'lead/view_Snooze',
    ];

    public $oldRoutes = ['/lead/view', '/lead/take', '/lead-change-state/take-over', '/lead-change-state/validate-take-over'];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        foreach ($this->oldRoutes as $oldRoute) {
            if ($route = $auth->getPermission($oldRoute)) {
                $auth->remove($route);
            }
        }

        (new RbacMigrationService())->up($this->adminPermissions, $this->adminRoles);
        (new RbacMigrationService())->up($this->simplePermissions, $this->simpleRoles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->adminPermissions, $this->adminRoles);
        (new RbacMigrationService())->down($this->simplePermissions, $this->simpleRoles);
    }
}
