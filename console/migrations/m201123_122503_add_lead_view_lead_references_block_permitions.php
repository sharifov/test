<?php

use yii\db\Migration;
use sales\rbac\rules\lead\view\leadPreferencesBlock\LeadViewLeadPreferencesBlockIsOwnerRule;
use sales\rbac\rules\lead\view\leadPreferencesBlock\LeadViewLeadPreferencesBlockEmptyOwnerRule;
use sales\rbac\rules\lead\view\leadPreferencesBlock\LeadViewLeadPreferencesBlockGroupRule;

/**
 * Class m201123_122503_add_lead_view_lead_references_block_permitions
 */
class m201123_122503_add_lead_view_lead_references_block_permitions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        if ($leadViewLeadPreferenceViewPermission = $auth->getPermission('lead/view_Lead_Preferences')) {
            $leadViewLeadPreferenceViewPermission->name = 'lead-view/lead-preferences/view';
            $auth->update('lead/view_Lead_Preferences', $leadViewLeadPreferenceViewPermission);

            $leadViewLeadPreferenceViewAllPermission = $auth->createPermission('lead-view/lead-preferences/view/all');
            $leadViewLeadPreferenceViewAllPermission->description = 'Lead Preference view all';
            $auth->add($leadViewLeadPreferenceViewAllPermission);
            $auth->addChild($leadViewLeadPreferenceViewAllPermission, $leadViewLeadPreferenceViewPermission);

            $leadViewLeadPreferenceBlockOwnerRule = new LeadViewLeadPreferencesBlockIsOwnerRule();
            $auth->add($leadViewLeadPreferenceBlockOwnerRule);
            $leadViewLeadPreferenceViewOwnerPermission = $auth->createPermission('lead-view/lead-preferences/view/owner');
            $leadViewLeadPreferenceViewOwnerPermission->description = 'Lead View Lead Preference Block user is Owner';
            $leadViewLeadPreferenceViewOwnerPermission->ruleName = $leadViewLeadPreferenceBlockOwnerRule->name;
            $auth->add($leadViewLeadPreferenceViewOwnerPermission);
            $auth->addChild($leadViewLeadPreferenceViewOwnerPermission, $leadViewLeadPreferenceViewPermission);

            $leadViewLeadPreferenceBlockEmptyRule = new LeadViewLeadPreferencesBlockEmptyOwnerRule();
            $auth->add($leadViewLeadPreferenceBlockEmptyRule);
            $leadViewLeadPreferenceViewEmptyPermission = $auth->createPermission('lead-view/lead-preferences/view/empty');
            $leadViewLeadPreferenceViewEmptyPermission->description = 'Lead View Lead Preference Block Owner is empty';
            $leadViewLeadPreferenceViewEmptyPermission->ruleName = $leadViewLeadPreferenceBlockEmptyRule->name;
            $auth->add($leadViewLeadPreferenceViewEmptyPermission);
            $auth->addChild($leadViewLeadPreferenceViewEmptyPermission, $leadViewLeadPreferenceViewPermission);

            $leadViewLeadPreferenceBlockGroupRule = new LeadViewLeadPreferencesBlockGroupRule();
            $auth->add($leadViewLeadPreferenceBlockGroupRule);
            $leadViewLeadPreferenceViewGroupPermission = $auth->createPermission('lead-view/lead-preferences/view/group');
            $leadViewLeadPreferenceViewGroupPermission->description = 'Lead View Lead Preference Block user is in common group with owner';
            $leadViewLeadPreferenceViewGroupPermission->ruleName = $leadViewLeadPreferenceBlockGroupRule->name;
            $auth->add($leadViewLeadPreferenceViewGroupPermission);
            $auth->addChild($leadViewLeadPreferenceViewGroupPermission, $leadViewLeadPreferenceViewPermission);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $rules = [
            'LeadViewLeadPreferencesBlockIsOwnerRule',
            'LeadViewLeadPreferencesBlockEmptyOwnerRule',
            'LeadViewLeadPreferencesBlockGroupRule'
        ];

        $permissions = [
            'lead-view/lead-preferences/view/all',
            'lead-view/lead-preferences/view/owner',
            'lead-view/lead-preferences/view/empty',
            'lead-view/lead-preferences/view/group'
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

        if ($leadViewLeadPreferenceView = $auth->getPermission('lead-view/lead-preferences/view')) {
            $leadViewLeadPreferenceView->name = 'lead/view_Lead_Preferences';
            $auth->update('lead-view/lead-preferences/view', $leadViewLeadPreferenceView);
        }
    }

}
