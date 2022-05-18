<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220518_134532_add_permission_user_multiple_update
 */
class m220518_134532_add_permission_user_multiple_update extends Migration
{
    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_SALES_SENIOR,
        Employee::ROLE_EXCHANGE_SENIOR,
        Employee::ROLE_SUPPORT_SENIOR,
        Employee::ROLE_SUPERVISION,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $employeeMultipleUpdatePermission = $auth->createPermission('employee/multipleUpdate');
        $employeeMultipleUpdatePermission->description = 'Employee multiple update';
        $auth->add($employeeMultipleUpdatePermission);

        foreach ($this->roles as $roleItem) {
            if ($role = $auth->getRole($roleItem)) {
                $auth->addChild($role, $employeeMultipleUpdatePermission);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $authManager = Yii::$app->authManager;

        if ($employeeMultipleUpdatePermission = $authManager->getPermission('employee/multipleUpdate')) {
            $authManager->remove($employeeMultipleUpdatePermission);
        }
    }
}
