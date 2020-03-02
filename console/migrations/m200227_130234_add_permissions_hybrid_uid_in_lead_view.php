<?php

use common\models\Employee;
use sales\rbac\rules\lead\view\LeadViewHybridUidByStatusRule;
use yii\db\Migration;
use yii\rbac\ManagerInterface;

/**
 * Class m200227_130234_add_permissions_hybrid_uid_in_lead_vieiw
 */
class m200227_130234_add_permissions_hybrid_uid_in_lead_view extends Migration
{
    private $rolesAdmin = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    private $rolesSimple = [
        Employee::ROLE_AGENT,
        Employee::ROLE_SUPERVISION,
        Employee::ROLE_QA,
        Employee::ROLE_USER_MANAGER,
        Employee::ROLE_SUP_AGENT,
        Employee::ROLE_SUP_SUPER,
        Employee::ROLE_EX_AGENT,
        Employee::ROLE_EX_SUPER,
        Employee::ROLE_SALES_SENIOR,
        Employee::ROLE_EXCHANGE_SENIOR,
        Employee::ROLE_SUPPORT_SENIOR,
    ];

    public function safeUp(): void
    {
        $authManager = Yii::$app->authManager;

        $leadViewHybridUidPermission = $authManager->createPermission('lead/view_HybridUid_View');
        $leadViewHybridUidPermission->description = 'Lead View HybridUid';
        $authManager->add($leadViewHybridUidPermission);

        $leadViewHybridUidByStatusRule = new LeadViewHybridUidByStatusRule();
        $authManager->add($leadViewHybridUidByStatusRule);

        $leadViewHybridUidByStatusPermission = $authManager->createPermission('lead/view_HybridUid_ViewByStatus');
        $leadViewHybridUidByStatusPermission->description = 'Lead View HybridUid View By Lead Status';
        $leadViewHybridUidByStatusPermission->ruleName = $leadViewHybridUidByStatusRule->name;

        $authManager->add($leadViewHybridUidByStatusPermission);
        $authManager->addChild($leadViewHybridUidByStatusPermission, $leadViewHybridUidPermission);

        foreach ($this->rolesAdmin as $admin) {
            if ($role = $authManager->getRole($admin)) {
                $authManager->addChild($role, $leadViewHybridUidPermission);
            }
        }
        foreach ($this->rolesSimple as $simple) {
            if ($role = $authManager->getRole($simple)) {
                $authManager->addChild($role, $leadViewHybridUidByStatusPermission);
            }
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    public function safeDown(): void
    {
        $authManager = Yii::$app->authManager;

        if ($leadViewHybridUidByStatusPermission = $authManager->getPermission('lead/view_HybridUid_ViewByStatus')) {
            $authManager->remove($leadViewHybridUidByStatusPermission);
        }
        if ($leadViewHybridUidByStatusRule = $authManager->getRule('lead/view_HybridUid_ViewByStatusRule')) {
            $authManager->remove($leadViewHybridUidByStatusRule);
        }
        if ($leadViewHybridUidPermission = $authManager->getPermission('lead/view_HybridUid_View')) {
            $authManager->remove($leadViewHybridUidPermission);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
