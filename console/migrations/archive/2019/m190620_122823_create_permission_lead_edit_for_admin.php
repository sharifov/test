<?php

use yii\db\Migration;

/**
 * Class m190620_122823_create_permissoin_lead_edit_for_admin
 */
class m190620_122823_create_permission_lead_edit_for_admin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $admin = $auth->getRole('admin');
        $updateLead = $auth->getPermission('updateLead');
        $auth->removeChild($admin, $updateLead);

        $updateLeadAdminRule = new \sales\rbac\rules\LeadAdminRule();
        $auth->add($updateLeadAdminRule);

        $updateLeadAdmin = $auth->createPermission('updateLeadAdmin');
        $updateLeadAdmin->description = 'Update Lead Admin';
        $updateLeadAdmin->ruleName = $updateLeadAdminRule->name;
        $auth->add($updateLeadAdmin);

        $auth->addChild($updateLeadAdmin, $updateLead);
        $auth->addChild($admin, $updateLeadAdmin);

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

        $updateLeadAdminRule = $auth->getRule('isLeadAdmin');
        $updateLeadAdmin = $auth->getPermission('updateLeadAdmin');
        $auth->remove($updateLeadAdmin);
        $auth->remove($updateLeadAdminRule);

        $updateLead = $auth->getPermission('updateLead');
        $admin = $auth->getRole('admin');
        $auth->addChild($admin, $updateLead);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

    }

}
