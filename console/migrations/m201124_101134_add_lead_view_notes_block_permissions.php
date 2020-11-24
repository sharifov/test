<?php

use yii\db\Migration;
use sales\rbac\rules\lead\view\notesBlock\LeadViewNotesBlockEmptyOwnerRule;
use sales\rbac\rules\lead\view\notesBlock\LeadViewNotesBlockGroupRule;
use sales\rbac\rules\lead\view\notesBlock\LeadViewNotesBlockIsOwnerRule;

/**
 * Class m201124_101134_add_lead_view_notes_block_permissions
 */
class m201124_101134_add_lead_view_notes_block_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $leadViewNotesViewPermission = $auth->createPermission('lead-view/notes/view');
        $leadViewNotesViewPermission->description = 'Notes view';
        $auth->add($leadViewNotesViewPermission);

        $leadViewNotesViewAllPermission = $auth->createPermission('lead-view/notes/view/all');
        $leadViewNotesViewAllPermission->description = 'Notes view all';
        $auth->add($leadViewNotesViewAllPermission);
        $auth->addChild($leadViewNotesViewAllPermission, $leadViewNotesViewPermission);

        $leadViewNotesBlockOwnerRule = new LeadViewNotesBlockIsOwnerRule();
        $auth->add($leadViewNotesBlockOwnerRule);
        $leadViewNotesViewOwnerPermission = $auth->createPermission('lead-view/notes/view/owner');
        $leadViewNotesViewOwnerPermission->description = 'Lead View Notes Block user is Owner';
        $leadViewNotesViewOwnerPermission->ruleName = $leadViewNotesBlockOwnerRule->name;
        $auth->add($leadViewNotesViewOwnerPermission);
        $auth->addChild($leadViewNotesViewOwnerPermission, $leadViewNotesViewPermission);

        $leadViewNotesBlockEmptyRule = new LeadViewNotesBlockEmptyOwnerRule();
        $auth->add($leadViewNotesBlockEmptyRule);
        $leadViewNotesViewEmptyPermission = $auth->createPermission('lead-view/notes/view/empty');
        $leadViewNotesViewEmptyPermission->description = 'Lead View Notes Block Owner is empty';
        $leadViewNotesViewEmptyPermission->ruleName = $leadViewNotesBlockEmptyRule->name;
        $auth->add($leadViewNotesViewEmptyPermission);
        $auth->addChild($leadViewNotesViewEmptyPermission, $leadViewNotesViewPermission);

        $leadViewNotesBlockGroupRule = new LeadViewNotesBlockGroupRule();
        $auth->add($leadViewNotesBlockGroupRule);
        $leadViewNotesViewGroupPermission = $auth->createPermission('lead-view/notes/view/group');
        $leadViewNotesViewGroupPermission->description = 'Lead View Notes Block user is in common group with owner';
        $leadViewNotesViewGroupPermission->ruleName = $leadViewNotesBlockGroupRule->name;
        $auth->add($leadViewNotesViewGroupPermission);
        $auth->addChild($leadViewNotesViewGroupPermission, $leadViewNotesViewPermission);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $rules = [
            'LeadViewNotesBlockIsOwnerRule',
            'LeadViewNotesBlockEmptyOwnerRule',
            'LeadViewNotesBlockGroupRule'
        ];

        $permissions = [
            'lead-view/notes/view',
            'lead-view/notes/view/all',
            'lead-view/notes/view/owner',
            'lead-view/notes/view/empty',
            'lead-view/notes/view/group'
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
    }
}
