<?php

use yii\db\Migration;
use common\models\Employee;

/**
 * Class m201105_120929_add_permissions_to_blocks_client_info_and_lead_preferences
 */
class m201105_120929_add_permissions_to_blocks_client_info_and_lead_preferences extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $superAdmin = $auth->getRole(Employee::ROLE_SUPER_ADMIN);
        $admin = $auth->getRole(Employee::ROLE_ADMIN);
        $agent = $auth->getRole(Employee::ROLE_AGENT);
        $supervision = $auth->getRole(Employee::ROLE_SUPERVISION);
        $qa = $auth->getRole(Employee::ROLE_QA);
        $qa_super = $auth->getRole(Employee::ROLE_QA_SUPER);
        $sup_agent = $auth->getRole(Employee::ROLE_SUP_AGENT);
        $sup_super = $auth->getRole(Employee::ROLE_SUP_SUPER);
        $ex_agent = $auth->getRole(Employee::ROLE_EX_AGENT);
        $ex_super = $auth->getRole(Employee::ROLE_EX_SUPER);

        $clientInfo = $auth->createPermission('lead/view_Client_Info');
        $clientInfo->description = 'Lead View Client Info';
        $auth->add($clientInfo);
        $auth->addChild($superAdmin, $clientInfo);
        $auth->addChild($admin, $clientInfo);
        $auth->addChild($agent, $clientInfo);
        $auth->addChild($supervision, $clientInfo);
        $auth->addChild($qa, $clientInfo);
        $auth->addChild($qa_super, $clientInfo);
        $auth->addChild($sup_agent, $clientInfo);
        $auth->addChild($sup_super, $clientInfo);
        $auth->addChild($ex_agent, $clientInfo);
        $auth->addChild($ex_super, $clientInfo);


        $leadPreferences = $auth->createPermission('lead/view_Lead_Preferences');
        $leadPreferences->description = 'Lead View Lead Preferences';
        $auth->add($leadPreferences);
        $auth->addChild($superAdmin, $leadPreferences);
        $auth->addChild($admin, $leadPreferences);
        $auth->addChild($agent, $leadPreferences);
        $auth->addChild($supervision, $leadPreferences);
        $auth->addChild($qa, $leadPreferences);
        $auth->addChild($qa_super, $leadPreferences);
        $auth->addChild($sup_agent, $leadPreferences);
        $auth->addChild($sup_super, $leadPreferences);
        $auth->addChild($ex_agent, $leadPreferences);
        $auth->addChild($ex_super, $leadPreferences);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        if ($permission = $auth->getPermission('lead/view_Client_Info')) {
            $auth->remove($permission);
        }

        if ($permission = $auth->getPermission('lead/view_Lead_Preferences')) {
            $auth->remove($permission);
        }
    }

}
