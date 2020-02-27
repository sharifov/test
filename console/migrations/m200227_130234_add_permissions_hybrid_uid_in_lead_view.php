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

    /** @var ManagerInterface $authManager */
    private $authManager;

    public function init()
    {
        parent::init();
        $this->authManager = Yii::$app->authManager;
    }

    public function safeUp(): void
    {
        $leadViewHybridUidPermission = $this->authManager->createPermission('lead/view_HybridUid_View');
        $leadViewHybridUidPermission->description = 'Lead View HybridUid';
        $this->authManager->add($leadViewHybridUidPermission);

        if ($admin = $this->authManager->getRole(Employee::ROLE_ADMIN)) {
            $this->authManager->addChild($admin, $leadViewHybridUidPermission);
        }
        /*
        $leadViewHybridUidByStatusRule = new LeadViewHybridUidByStatusRule();
        $this->authManager->add($leadViewHybridUidByStatusRule);

        $leadViewHybridUidByStatusPermission = $this->authManager->createPermission('lead/view_HybridUid_ViewByStatus');
        $leadViewHybridUidByStatusPermission->description = 'Lead View HybridUid View By Lead Status';
        $leadViewHybridUidByStatusPermission->ruleName = $leadViewHybridUidByStatusRule->name;

        $this->authManager->add($leadViewHybridUidByStatusPermission);
        $this->authManager->addChild($leadViewHybridUidByStatusPermission, $leadViewHybridUidPermission);

        foreach ($this->rolesAdmin as $admin) {
            if ($role = $this->authManager->getRole($admin)) {
                $this->authManager->addChild($role, $leadViewHybridUidPermission);
            }
        }
        foreach ($this->rolesSimple as $simple) {
            if ($role = $this->authManager->getRole($simple)) {
                $this->authManager->addChild($role, $leadViewHybridUidByStatusPermission);
            }
        }
        */
    }

    public function safeDown(): void
    {
        /*
        if ($leadViewHybridUidByStatusRule = $this->authManager->getRule('lead/view_HybridUid_ViewByStatusRule')) {
            $this->authManager->remove($leadViewHybridUidByStatusRule);
        }
        */
        if ($leadViewHybridUidPermission = $this->authManager->getPermission('lead/view_HybridUid_View')) {
            $this->authManager->remove($leadViewHybridUidPermission);
        }
        /*
        if ($leadViewHybridUidByStatusPermission = $this->authManager->getPermission('lead/view_HybridUid_ViewByStatus')) {
            $this->authManager->remove($leadViewHybridUidByStatusPermission);
        }
        */
    }
}
