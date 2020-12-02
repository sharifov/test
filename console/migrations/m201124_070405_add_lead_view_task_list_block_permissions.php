<?php

use yii\db\Migration;
use sales\rbac\rules\lead\view\taskListBlock\LeadViewTaskListBlockGroupRule;
use sales\rbac\rules\lead\view\taskListBlock\LeadViewTaskListBlockEmptyOwnerRule;
use sales\rbac\rules\lead\view\taskListBlock\LeadViewTaskListBlockIsOwnerRule;

/**
 * Class m201124_070405_add_lead_view_task_list_block_permissions
 */
class m201124_070405_add_lead_view_task_list_block_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        if ($leadViewTaskListViewPermission = $auth->getPermission('lead/view_Task_List')) {
            $leadViewTaskListViewPermission->name = 'lead-view/task-list/view';
            $auth->update('lead/view_Task_List', $leadViewTaskListViewPermission);

            $leadViewTaskListViewAllPermission = $auth->createPermission('lead-view/task-list/view/all');
            $leadViewTaskListViewAllPermission->description = 'Task List view all';
            $auth->add($leadViewTaskListViewAllPermission);
            $auth->addChild($leadViewTaskListViewAllPermission, $leadViewTaskListViewPermission);

            $leadViewTaskListBlockOwnerRule = new LeadViewTaskListBlockIsOwnerRule();
            $auth->add($leadViewTaskListBlockOwnerRule);
            $leadViewTaskListViewOwnerPermission = $auth->createPermission('lead-view/task-list/view/owner');
            $leadViewTaskListViewOwnerPermission->description = 'Lead View Task List Block user is Owner';
            $leadViewTaskListViewOwnerPermission->ruleName = $leadViewTaskListBlockOwnerRule->name;
            $auth->add($leadViewTaskListViewOwnerPermission);
            $auth->addChild($leadViewTaskListViewOwnerPermission, $leadViewTaskListViewPermission);

            $leadViewTaskListBlockEmptyRule = new LeadViewTaskListBlockEmptyOwnerRule();
            $auth->add($leadViewTaskListBlockEmptyRule);
            $leadViewTaskListViewEmptyPermission = $auth->createPermission('lead-view/task-list/view/empty');
            $leadViewTaskListViewEmptyPermission->description = 'Lead View Task List Block Owner is empty';
            $leadViewTaskListViewEmptyPermission->ruleName = $leadViewTaskListBlockEmptyRule->name;
            $auth->add($leadViewTaskListViewEmptyPermission);
            $auth->addChild($leadViewTaskListViewEmptyPermission, $leadViewTaskListViewPermission);

            $leadViewTaskListBlockGroupRule = new LeadViewTaskListBlockGroupRule();
            $auth->add($leadViewTaskListBlockGroupRule);
            $leadViewTaskListViewGroupPermission = $auth->createPermission('lead-view/task-list/view/group');
            $leadViewTaskListViewGroupPermission->description = 'Lead View Task List Block user is in common group with owner';
            $leadViewTaskListViewGroupPermission->ruleName = $leadViewTaskListBlockGroupRule->name;
            $auth->add($leadViewTaskListViewGroupPermission);
            $auth->addChild($leadViewTaskListViewGroupPermission, $leadViewTaskListViewPermission);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $rules = [
            'LeadViewTaskListBlockIsOwnerRule',
            'LeadViewTaskListBlockEmptyOwnerRule',
            'LeadViewTaskListBlockGroupRule'
        ];

        $permissions = [
            'lead-view/task-list/view/all',
            'lead-view/task-list/view/owner',
            'lead-view/task-list/view/empty',
            'lead-view/task-list/view/group'
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

        if ($leadViewTaskListView = $auth->getPermission('lead-view/task-list/view')) {
            $leadViewTaskListView->name = 'lead/view_Task_List';
            $auth->update('lead-view/task-list/view', $leadViewTaskListView);
        }
    }


}
