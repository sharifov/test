<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200504_083333_add_permissions_for_all_roles_page_lead_view
 */
class m200504_083333_add_permissions_for_all_roles_page_lead_view extends Migration
{
    public $roles = [
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

    public $permissions = [
        'lead/view_Check_List',
        'lead/view_Task_List',
        'lead/view_BO_Expert',
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        foreach ($this->permissions as $item) {
            if ($permission = $auth->getPermission($item)) {
                foreach ($this->roles as $r) {
                    if ($role = $auth->getRole($r)) {
                        if (!$auth->hasChild($role, $permission) && $auth->canAddChild($role, $permission)) {
                            $auth->addChild($role, $permission);
                        }
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        foreach ($this->permissions as $item) {
            if ($permission = $auth->getPermission($item)) {
                foreach ($this->roles as $r) {
                    if ($role = $auth->getRole($r)) {
                        if ($auth->hasChild($role, $permission)) {
                            $auth->removeChild($role, $permission);
                        }
                    }
                }
            }
        }
    }
}
