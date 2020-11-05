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

        $admin = $auth->getRole(Employee::ROLE_ADMIN);
        $superAdmin = $auth->getRole(Employee::ROLE_SUPER_ADMIN);

        $checkList = $auth->createPermission('lead/view_Client_Info');
        $checkList->description = 'Lead View Client Info';
        $auth->add($checkList);
        $auth->addChild($admin, $checkList);
        $auth->addChild($superAdmin, $checkList);

        $taskList = $auth->createPermission('lead/view_Lead_Preferences');
        $taskList->description = 'Lead View Lead Preferences';
        $auth->add($taskList);
        $auth->addChild($admin, $taskList);
        $auth->addChild($superAdmin, $taskList);
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
