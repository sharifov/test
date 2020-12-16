<?php

use yii\db\Migration;
use sales\rbac\rules\lead\view\boExpertBlock\LeadViewBoExpertBlockGroupRule;
use sales\rbac\rules\lead\view\boExpertBlock\LeadViewBoExpertBlockEmptyOwnerRule;
use sales\rbac\rules\lead\view\boExpertBlock\LeadViewBoExpertBlockIsOwnerRule;

/**
 * Class m201124_093813_add_lead_view_bo_expert_block_permissions
 */
class m201124_093813_add_lead_view_bo_expert_block_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        if ($leadViewBoExpertViewPermission = $auth->getPermission('lead/view_BO_Expert')) {
            $leadViewBoExpertViewPermission->name = 'lead-view/call-expert/view';
            $auth->update('lead/view_BO_Expert', $leadViewBoExpertViewPermission);

            $leadViewBoExpertViewAllPermission = $auth->createPermission('lead-view/call-expert/view/all');
            $leadViewBoExpertViewAllPermission->description = 'Bo Expert view all';
            $auth->add($leadViewBoExpertViewAllPermission);
            $auth->addChild($leadViewBoExpertViewAllPermission, $leadViewBoExpertViewPermission);

            $leadViewBoExpertBlockOwnerRule = new LeadViewBoExpertBlockIsOwnerRule();
            $auth->add($leadViewBoExpertBlockOwnerRule);
            $leadViewBoExpertViewOwnerPermission = $auth->createPermission('lead-view/call-expert/view/owner');
            $leadViewBoExpertViewOwnerPermission->description = 'Lead View Bo Expert Block user is Owner';
            $leadViewBoExpertViewOwnerPermission->ruleName = $leadViewBoExpertBlockOwnerRule->name;
            $auth->add($leadViewBoExpertViewOwnerPermission);
            $auth->addChild($leadViewBoExpertViewOwnerPermission, $leadViewBoExpertViewPermission);

            $leadViewBoExpertBlockEmptyRule = new LeadViewBoExpertBlockEmptyOwnerRule();
            $auth->add($leadViewBoExpertBlockEmptyRule);
            $leadViewBoExpertViewEmptyPermission = $auth->createPermission('lead-view/call-expert/view/empty');
            $leadViewBoExpertViewEmptyPermission->description = 'Lead View Bo Expert Block Owner is empty';
            $leadViewBoExpertViewEmptyPermission->ruleName = $leadViewBoExpertBlockEmptyRule->name;
            $auth->add($leadViewBoExpertViewEmptyPermission);
            $auth->addChild($leadViewBoExpertViewEmptyPermission, $leadViewBoExpertViewPermission);

            $leadViewBoExpertBlockGroupRule = new LeadViewBoExpertBlockGroupRule();
            $auth->add($leadViewBoExpertBlockGroupRule);
            $leadViewBoExpertViewGroupPermission = $auth->createPermission('lead-view/call-expert/view/group');
            $leadViewBoExpertViewGroupPermission->description = 'Lead View Bo Expert Block user is in common group with owner';
            $leadViewBoExpertViewGroupPermission->ruleName = $leadViewBoExpertBlockGroupRule->name;
            $auth->add($leadViewBoExpertViewGroupPermission);
            $auth->addChild($leadViewBoExpertViewGroupPermission, $leadViewBoExpertViewPermission);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $rules = [
            'LeadViewBoExpertBlockIsOwnerRule',
            'LeadViewBoExpertBlockEmptyOwnerRule',
            'LeadViewBoExpertBlockGroupRule'
        ];

        $permissions = [
            'lead-view/call-expert/view/all',
            'lead-view/call-expert/view/owner',
            'lead-view/call-expert/view/empty',
            'lead-view/call-expert/view/group'
        ];

        foreach ($permissions as $permissionName) {
            if ($permission = $auth->getPermission($permissionName)) {
                $auth->remove($permission);
            }
        }

        foreach ($rules as $ruleName) {
            if ($rule = $auth->getRule($ruleName)) {
                $auth->remove($rule);
            }
        }

        if ($leadViewBoExpertView = $auth->getPermission('lead-view/call-expert/view')) {
            $leadViewBoExpertView->name = 'lead/view_BO_Expert';
            $auth->update('lead-view/call-expert/view', $leadViewBoExpertView);
        }
    }
}
