<?php

use yii\db\Migration;
use sales\rbac\rules\lead\view\communicationBlock\LeadViewCommunicationBlockCommonGroupRule;
use sales\rbac\rules\lead\view\communicationBlock\LeadViewCommunicationBlockEmptyOwnerRule;
use sales\rbac\rules\lead\view\communicationBlock\LeadViewCommunicationBlockIsOwnerRule;

/**
 * Class m201124_082506_add_lead_view_communication_block_permissions
 */
class m201124_082506_add_lead_view_communication_block_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        if ($leadViewCommunicationViewPermission = $auth->getPermission('lead/view_CommunicationBlock')) {
            $leadViewCommunicationViewPermission->name = 'lead-view/communication-block/view';
            $auth->update('lead/view_CommunicationBlock', $leadViewCommunicationViewPermission);

            $leadViewCommunicationViewAllPermission = $auth->createPermission('lead-view/communication-block/view/all');
            $leadViewCommunicationViewAllPermission->description = 'Communication view all';
            $auth->add($leadViewCommunicationViewAllPermission);
            $auth->addChild($leadViewCommunicationViewAllPermission, $leadViewCommunicationViewPermission);

            $leadViewCommunicationBlockOwnerRule = new LeadViewCommunicationBlockIsOwnerRule();
            $auth->add($leadViewCommunicationBlockOwnerRule);
            $leadViewCommunicationViewOwnerPermission = $auth->createPermission('lead-view/communication-block/view/owner');
            $leadViewCommunicationViewOwnerPermission->description = 'Lead View Communication Block user is Owner';
            $leadViewCommunicationViewOwnerPermission->ruleName = $leadViewCommunicationBlockOwnerRule->name;
            $auth->add($leadViewCommunicationViewOwnerPermission);
            $auth->addChild($leadViewCommunicationViewOwnerPermission, $leadViewCommunicationViewPermission);

            $leadViewCommunicationBlockEmptyRule = new LeadViewCommunicationBlockEmptyOwnerRule();
            $auth->add($leadViewCommunicationBlockEmptyRule);
            $leadViewCommunicationViewEmptyPermission = $auth->createPermission('lead-view/communication-block/view/empty');
            $leadViewCommunicationViewEmptyPermission->description = 'Lead View Communication Block Owner is empty';
            $leadViewCommunicationViewEmptyPermission->ruleName = $leadViewCommunicationBlockEmptyRule->name;
            $auth->add($leadViewCommunicationViewEmptyPermission);
            $auth->addChild($leadViewCommunicationViewEmptyPermission, $leadViewCommunicationViewPermission);

            $leadViewCommunicationBlockGroupRule = new LeadViewCommunicationBlockCommonGroupRule();
            $auth->add($leadViewCommunicationBlockGroupRule);
            $leadViewCommunicationViewGroupPermission = $auth->createPermission('lead-view/communication-block/view/group');
            $leadViewCommunicationViewGroupPermission->description = 'Lead View Communication Block user is in common group with owner';
            $leadViewCommunicationViewGroupPermission->ruleName = $leadViewCommunicationBlockGroupRule->name;
            $auth->add($leadViewCommunicationViewGroupPermission);
            $auth->addChild($leadViewCommunicationViewGroupPermission, $leadViewCommunicationViewPermission);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $rules = [
            'LeadViewCommunicationBlockIsOwnerRule',
            'LeadViewCommunicationBlockEmptyOwnerRule',
            'LeadViewCommunicationBlockCommonGroupRule'
        ];

        $permissions = [
            'lead-view/communication-block/view/all',
            'lead-view/communication-block/view/owner',
            'lead-view/communication-block/view/empty',
            'lead-view/communication-block/view/group'
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

        if ($leadViewCommunicationView = $auth->getPermission('lead-view/communication-block/view')) {
            $leadViewCommunicationView->name = 'lead/view_CommunicationBlock';
            $auth->update('lead-view/communication-block/view', $leadViewCommunicationView);
        }
    }
}
