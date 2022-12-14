<?php

use yii\db\Migration;

/**
 * Class m190619_110832_create_roles_for_manage_lead
 */
class m190619_110832_create_roles_for_manage_lead extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $admin = $auth->getRole('admin');
        $agent = $auth->getRole('agent');
        $supervision = $auth->getRole('supervision');

        //----------------------------------------------------------
        $createLead = $auth->createPermission('createLead');
        $createLead->description = 'Create Lead';
        $auth->add($createLead);

        $auth->addChild($admin, $createLead);
        $auth->addChild($agent, $createLead);
        $auth->addChild($supervision, $createLead);
        //----------------------------------------------------------

        //----------------------------------------------------------
        $updateLead = $auth->createPermission('updateLead');
        $updateLead->description = 'Update Lead';
        $auth->add($updateLead);

        $auth->addChild($admin, $updateLead);
        //----------------------------------------------------------

        //----------------------------------------------------------
        $leadOwnerRule = new \src\rbac\rules\LeadOwnerRule();
        $auth->add($leadOwnerRule);

        $updateOwnLead = $auth->createPermission('updateOwnLead');
        $updateOwnLead->description = 'Update Own Lead';
        $updateOwnLead->ruleName = $leadOwnerRule->name;
        $auth->add($updateOwnLead);

        $auth->addChild($updateOwnLead, $updateLead);
        $auth->addChild($agent, $updateOwnLead);
        //----------------------------------------------------------

        //----------------------------------------------------------
        $leadGroupRule = new \src\rbac\rules\LeadSupervisionRule();
        $auth->add($leadGroupRule);
        $updateLeadGroup = $auth->createPermission('updateLeadSupervision');
        $updateLeadGroup->description = 'Update Lead Supervision';
        $updateLeadGroup->ruleName = $leadGroupRule->name;
        $auth->add($updateLeadGroup);
        $auth->addChild($updateLeadGroup, $updateLead);

        $auth->addChild($supervision, $updateLeadGroup);
        //----------------------------------------------------------


        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $createLead = $auth->getPermission('createLead');
        $updateLead = $auth->getPermission('updateLead');
        $updateOwnLead = $auth->getPermission('updateOwnLead');
        $updateLeadGroup = $auth->getPermission('updateLeadSupervision');

        $leadOwnerRule = $auth->getRule('isLeadOwner');
        $leadGroupRule = $auth->getRule('isLeadSupervision');
        $auth->remove($leadOwnerRule);
        $auth->remove($leadGroupRule);
        $auth->remove($createLead);
        $auth->remove($updateLead);
        $auth->remove($updateOwnLead);
        $auth->remove($updateLeadGroup);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
