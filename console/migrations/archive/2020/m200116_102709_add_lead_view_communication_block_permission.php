<?php

use common\models\Employee;
use sales\rbac\rules\lead\view\communicationBlock\LeadViewCommunicationBlockCommonGroupRule;
use sales\rbac\rules\lead\view\communicationBlock\LeadViewCommunicationBlockEmptyOwnerRule;
use sales\rbac\rules\lead\view\communicationBlock\LeadViewCommunicationBlockIsOwnerRule;
use yii\db\Migration;

/**
 * Class m200116_102709_add_lead_view_communication_block_permission
 */
class m200116_102709_add_lead_view_communication_block_permission extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $leadViewCommunicationBlock = $auth->createPermission('lead/view_CommunicationBlock');
        $leadViewCommunicationBlock->description = 'Lead View Communication Block';
        $auth->add($leadViewCommunicationBlock);

        $leadViewCommunicationBlockIsOwnerRule = new LeadViewCommunicationBlockIsOwnerRule([
            'name' => 'lead/view_CommunicationBlockIsOwnerRule',
        ]);
        $auth->add($leadViewCommunicationBlockIsOwnerRule);
        $leadViewCommunicationBlockIsOwner = $auth->createPermission('lead/view_CommunicationBlockIsOwner');
        $leadViewCommunicationBlockIsOwner->description = 'Lead View Communication Block Is Owner';
        $leadViewCommunicationBlockIsOwner->ruleName = $leadViewCommunicationBlockIsOwnerRule->name;
        $auth->add($leadViewCommunicationBlockIsOwner);
        $auth->addChild($leadViewCommunicationBlockIsOwner, $leadViewCommunicationBlock);

        $leadViewCommunicationBlockEmptyOwnerRule = new LeadViewCommunicationBlockEmptyOwnerRule([
            'name' => 'lead/view_CommunicationBlockEmptyOwnerRule',
        ]);
        $auth->add($leadViewCommunicationBlockEmptyOwnerRule);
        $leadViewCommunicationBlockEmptyOwner = $auth->createPermission('lead/view_CommunicationBlockEmptyOwner');
        $leadViewCommunicationBlockEmptyOwner->description = 'Lead View Communication Block Empty Owner';
        $leadViewCommunicationBlockEmptyOwner->ruleName = $leadViewCommunicationBlockEmptyOwnerRule->name;
        $auth->add($leadViewCommunicationBlockEmptyOwner);
        $auth->addChild($leadViewCommunicationBlockEmptyOwner, $leadViewCommunicationBlock);

        $leadViewCommunicationBlockCommonGroupRule = new LeadViewCommunicationBlockCommonGroupRule([
            'name' => 'lead/view_CommunicationBlockCommonGroupRule',
        ]);
        $auth->add($leadViewCommunicationBlockCommonGroupRule);
        $leadViewCommunicationBlockCommonGroup = $auth->createPermission('lead/view_CommunicationBlockCommonGroup');
        $leadViewCommunicationBlockCommonGroup->description = 'Lead View Communication Block Common Group';
        $leadViewCommunicationBlockCommonGroup->ruleName = $leadViewCommunicationBlockCommonGroupRule->name;
        $auth->add($leadViewCommunicationBlockCommonGroup);
        $auth->addChild($leadViewCommunicationBlockCommonGroup, $leadViewCommunicationBlock);

        if ($admin = $auth->getRole(Employee::ROLE_ADMIN)) {
            $auth->addChild($admin, $leadViewCommunicationBlock);
        }
         if ($admin = $auth->getRole(Employee::ROLE_SUPPORT_SENIOR)) {
            $auth->addChild($admin, $leadViewCommunicationBlock);
        }
         if ($admin = $auth->getRole(Employee::ROLE_EXCHANGE_SENIOR)) {
            $auth->addChild($admin, $leadViewCommunicationBlock);
        }
         if ($admin = $auth->getRole(Employee::ROLE_SALES_SENIOR)) {
            $auth->addChild($admin, $leadViewCommunicationBlock);
        }
        if ($qa = $auth->getRole(Employee::ROLE_QA)) {
            $auth->addChild($qa, $leadViewCommunicationBlock);
        }
        if ($supervision = $auth->getRole(Employee::ROLE_SUPERVISION)) {
            $auth->addChild($supervision, $leadViewCommunicationBlockIsOwner);
            $auth->addChild($supervision, $leadViewCommunicationBlockEmptyOwner);
            $auth->addChild($supervision, $leadViewCommunicationBlockCommonGroup);
        }
        if ($supervision = $auth->getRole(Employee::ROLE_AGENT)) {
            $auth->addChild($supervision, $leadViewCommunicationBlockIsOwner);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        if ($leadViewCommunicationBlockCommonGroup = $auth->getPermission('lead/view_CommunicationBlockCommonGroup')) {
            $auth->remove($leadViewCommunicationBlockCommonGroup);
        }

        if ($leadViewCommunicationBlockCommonGroupRule = $auth->getRule('lead/view_CommunicationBlockCommonGroupRule')) {
            $auth->remove($leadViewCommunicationBlockCommonGroupRule);
        }

        if ($leadViewCommunicationBlockEmptyOwner = $auth->getPermission('lead/view_CommunicationBlockEmptyOwner')) {
            $auth->remove($leadViewCommunicationBlockEmptyOwner);
        }

        if ($leadViewCommunicationBlockEmptyOwnerRule = $auth->getRule('lead/view_CommunicationBlockEmptyOwnerRule')) {
            $auth->remove($leadViewCommunicationBlockEmptyOwnerRule);
        }

        if ($leadViewCommunicationBlockIsOwner = $auth->getPermission('lead/view_CommunicationBlockIsOwner')) {
            $auth->remove($leadViewCommunicationBlockIsOwner);
        }

        if ($leadViewCommunicationBlockIsOwnerRule = $auth->getRule('lead/view_CommunicationBlockIsOwnerRule')) {
            $auth->remove($leadViewCommunicationBlockIsOwnerRule);
        }

        if ($leadViewCommunicationBlock = $auth->getPermission('lead/view_CommunicationBlock')) {
            $auth->remove($leadViewCommunicationBlock);
        }
    }
}
