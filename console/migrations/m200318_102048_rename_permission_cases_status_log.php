<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200318_102048_rename_permission_cases_status_log
 */
class m200318_102048_rename_permission_cases_status_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $roles = [
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

        if (!$caseStatusLog = $auth->getPermission('/case-status-log/index')) {
            $caseStatusLog = $auth->createPermission('/case-status-log/index');
            $auth->add($caseStatusLog);
        }

        foreach ($roles as $role) {
            foreach ($auth->getPermissionsByRole($role) as $permission) {
                if ($permission->name === '/cases-status-log/index' || $permission->name === '/cases-status-log/*') {
                    if ($roleItem = $auth->getRole($role)) {
                        if (!$auth->hasChild($roleItem, $caseStatusLog) && $auth->canAddChild($roleItem, $caseStatusLog)) {
                            $auth->addChild($roleItem, $caseStatusLog);
                        }
                    }
                }
            }
        }

        if ($permission = $auth->getPermission('/cases-status-log/index')) {
            $auth->remove($permission);
        }

        if ($permission = $auth->getPermission('/cases-status-log/*')) {
            $auth->remove($permission);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $roles = [
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

        if (!$casesStatusLog = $auth->getPermission('/cases-status-log/index')) {
            $casesStatusLog = $auth->createPermission('/cases-status-log/index');
            $auth->add($casesStatusLog);
        }

        foreach ($roles as $role) {
            foreach ($auth->getPermissionsByRole($role) as $permission) {
                if ($permission->name === '/case-status-log/index') {
                    if ($roleItem = $auth->getRole($role)) {
                        if (!$auth->hasChild($roleItem, $casesStatusLog) && $auth->canAddChild($roleItem, $casesStatusLog)) {
                            $auth->addChild($roleItem, $casesStatusLog);
                        }
                    }
                }
            }
        }

        if ($permission = $auth->getPermission('/case-status-log/index')) {
            $auth->remove($permission);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
