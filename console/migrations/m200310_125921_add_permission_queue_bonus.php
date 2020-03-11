<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200310_125921_add_permission_queue_bonus
 */
class m200310_125921_add_permission_queue_bonus extends Migration
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

        $permissionBonusQueue = $auth->createPermission('/lead/bonus');
        $auth->add($permissionBonusQueue);

        foreach ($roles as $role) {

            $permissions = $auth->getPermissionsByRole($role);

            foreach ($permissions as $permission) {
                if ($permission->name === '/lead/follow-up') {
                    if ($roleItem = $auth->getRole($role)) {
                        $auth->addChild($roleItem, $permissionBonusQueue);
                    }
                }
            }
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

        if ($permission = $auth->getPermission('/lead/bonus')) {
            $auth->remove($permission);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
