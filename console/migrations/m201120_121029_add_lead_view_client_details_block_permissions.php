<?php

use yii\db\Migration;
use sales\rbac\rules\lead\view\clientInfoBlock\LeadViewClientInfoBlockIsOwnerRule;
use sales\rbac\rules\lead\view\clientInfoBlock\LeadViewClientInfoBlockEmptyOwnerRule;
use sales\rbac\rules\lead\view\clientInfoBlock\LeadViewClientInfoBlockGroupRule;

/**
 * Class m201120_121029_add_lead_view_client_details_block_permissions
 */
class m201120_121029_add_lead_view_client_details_block_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        if ($leadViewClientInfoViewPermission = $auth->getPermission('lead/view_Client_Info')) {
            $leadViewClientInfoViewPermission->name = 'lead-view/client-info/view';
            $auth->update('lead/view_Client_Info', $leadViewClientInfoViewPermission);

            $leadViewClientInfoViewAllPermission = $auth->createPermission('lead-view/client-info/view/all');
            $leadViewClientInfoViewAllPermission->description = 'Client Info view all';
            $auth->add($leadViewClientInfoViewAllPermission);
            $auth->addChild($leadViewClientInfoViewAllPermission, $leadViewClientInfoViewPermission);

            $leadViewClientInfoBlockOwnerRule = new LeadViewClientInfoBlockIsOwnerRule();
            $auth->add($leadViewClientInfoBlockOwnerRule);
            $leadViewClientInfoViewOwnerPermission = $auth->createPermission('lead-view/client-info/view/owner');
            $leadViewClientInfoViewOwnerPermission->description = 'Lead View Client Info Block user is Owner';
            $leadViewClientInfoViewOwnerPermission->ruleName = $leadViewClientInfoBlockOwnerRule->name;
            $auth->add($leadViewClientInfoViewOwnerPermission);
            $auth->addChild($leadViewClientInfoViewOwnerPermission, $leadViewClientInfoViewPermission);

            $leadViewClientInfoBlockEmptyRule = new LeadViewClientInfoBlockEmptyOwnerRule();
            $auth->add($leadViewClientInfoBlockEmptyRule);
            $leadViewClientInfoViewEmptyPermission = $auth->createPermission('lead-view/client-info/view/empty');
            $leadViewClientInfoViewEmptyPermission->description = 'Lead View Client Info Block Owner is empty';
            $leadViewClientInfoViewEmptyPermission->ruleName = $leadViewClientInfoBlockEmptyRule->name;
            $auth->add($leadViewClientInfoViewEmptyPermission);
            $auth->addChild($leadViewClientInfoViewEmptyPermission, $leadViewClientInfoViewPermission);

            $leadViewClientInfoBlockGroupRule = new LeadViewClientInfoBlockGroupRule();
            $auth->add($leadViewClientInfoBlockGroupRule);
            $leadViewClientInfoViewGroupPermission = $auth->createPermission('lead-view/client-info/view/group');
            $leadViewClientInfoViewGroupPermission->description = 'Lead View Client Info Block user is in common group with owner';
            $leadViewClientInfoViewGroupPermission->ruleName = $leadViewClientInfoBlockGroupRule->name;
            $auth->add($leadViewClientInfoViewGroupPermission);
            $auth->addChild($leadViewClientInfoViewGroupPermission, $leadViewClientInfoViewPermission);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $rules = [
            'LeadViewClientInfoBlockIsOwnerRule',
            'LeadViewClientInfoBlockEmptyOwnerRule',
            'LeadViewClientInfoBlockGroupRule'
        ];

        $permissions = [
            'lead-view/client-info/view/all',
            'lead-view/client-info/view/owner',
            'lead-view/client-info/view/empty',
            'lead-view/client-info/view/group'
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

        if ($leadViewClientInfoView = $auth->getPermission('lead-view/client-info/view')) {
            $leadViewClientInfoView->name = 'lead/view_Client_Info';
            $auth->update('lead-view/client-info/view', $leadViewClientInfoView);
        }
    }
}
