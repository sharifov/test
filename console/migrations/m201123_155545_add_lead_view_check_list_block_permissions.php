<?php

use yii\db\Migration;
use sales\rbac\rules\lead\view\checkListBlock\LeadViewCheckListBlockEmptyOwnerRule;
use sales\rbac\rules\lead\view\checkListBlock\LeadViewCheckListBlockGroupRule;
use sales\rbac\rules\lead\view\checkListBlock\LeadViewCheckListBlockIsOwnerRule;

/**
 * Class m201123_155545_add_lead_view_check_list_block_permissions
 */
class m201123_155545_add_lead_view_check_list_block_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        if ($leadViewCheckListViewPermission = $auth->getPermission('lead/view_Check_List')) {
            $leadViewCheckListViewPermission->name = 'lead-view/check-list/view';
            $auth->update('lead/view_Check_List', $leadViewCheckListViewPermission);

            $leadViewCheckListViewAllPermission = $auth->createPermission('lead-view/check-list/view/all');
            $leadViewCheckListViewAllPermission->description = 'Check List view all';
            $auth->add($leadViewCheckListViewAllPermission);
            $auth->addChild($leadViewCheckListViewAllPermission, $leadViewCheckListViewPermission);

            $leadViewCheckListBlockOwnerRule = new LeadViewCheckListBlockIsOwnerRule();
            $auth->add($leadViewCheckListBlockOwnerRule);
            $leadViewCheckListViewOwnerPermission = $auth->createPermission('lead-view/check-list/view/owner');
            $leadViewCheckListViewOwnerPermission->description = 'Lead View Check List Block user is Owner';
            $leadViewCheckListViewOwnerPermission->ruleName = $leadViewCheckListBlockOwnerRule->name;
            $auth->add($leadViewCheckListViewOwnerPermission);
            $auth->addChild($leadViewCheckListViewOwnerPermission, $leadViewCheckListViewPermission);

            $leadViewCheckListBlockEmptyRule = new LeadViewCheckListBlockEmptyOwnerRule();
            $auth->add($leadViewCheckListBlockEmptyRule);
            $leadViewCheckListViewEmptyPermission = $auth->createPermission('lead-view/check-list/view/empty');
            $leadViewCheckListViewEmptyPermission->description = 'Lead View Check List Block Owner is empty';
            $leadViewCheckListViewEmptyPermission->ruleName = $leadViewCheckListBlockEmptyRule->name;
            $auth->add($leadViewCheckListViewEmptyPermission);
            $auth->addChild($leadViewCheckListViewEmptyPermission, $leadViewCheckListViewPermission);

            $leadViewCheckListBlockGroupRule = new LeadViewCheckListBlockGroupRule();
            $auth->add($leadViewCheckListBlockGroupRule);
            $leadViewCheckListViewGroupPermission = $auth->createPermission('lead-view/check-list/view/group');
            $leadViewCheckListViewGroupPermission->description = 'Lead View Check List Block user is in common group with owner';
            $leadViewCheckListViewGroupPermission->ruleName = $leadViewCheckListBlockGroupRule->name;
            $auth->add($leadViewCheckListViewGroupPermission);
            $auth->addChild($leadViewCheckListViewGroupPermission, $leadViewCheckListViewPermission);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $rules = [
            'LeadViewCheckListBlockIsOwnerRule',
            'LeadViewCheckListBlockEmptyOwnerRule',
            'LeadViewCheckListBlockGroupRule'
        ];

        $permissions = [
            'lead-view/check-list/view/all',
            'lead-view/check-list/view/owner',
            'lead-view/check-list/view/empty',
            'lead-view/check-list/view/group'
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

        if ($leadViewCheckListView = $auth->getPermission('lead-view/check-list/view')) {
            $leadViewCheckListView->name = 'lead/view_Check_List';
            $auth->update('lead-view/check-list/view', $leadViewCheckListView);
        }
    }

}
