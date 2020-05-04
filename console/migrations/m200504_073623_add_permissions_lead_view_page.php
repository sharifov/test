<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200504_073623_add_permissions_lead_view_page
 */
class m200504_073623_add_permissions_lead_view_page extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $admin = $auth->getRole(Employee::ROLE_ADMIN);
        $superAdmin = $auth->getRole(Employee::ROLE_SUPER_ADMIN);

        $checkList = $auth->createPermission('lead/view_Check_List');
        $checkList->description = 'Lead View Check List';
        $auth->add($checkList);
        $auth->addChild($admin, $checkList);
        $auth->addChild($superAdmin, $checkList);

        $taskList = $auth->createPermission('lead/view_Task_List');
        $taskList->description = 'Lead View Task List';
        $auth->add($taskList);
        $auth->addChild($admin, $taskList);
        $auth->addChild($superAdmin, $taskList);

        $boExpert = $auth->createPermission('lead/view_BO_Expert');
        $boExpert->description = 'Lead View BO Expert';
        $auth->add($boExpert);
        $auth->addChild($admin, $boExpert);
        $auth->addChild($superAdmin, $boExpert);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

       if ($permission = $auth->getPermission('lead/view_Check_List')) {
           $auth->remove($permission);
       }

       if ($permission = $auth->getPermission('lead/view_Task_List')) {
           $auth->remove($permission);
       }

       if ($permission = $auth->getPermission('lead/view_BO_Expert')) {
           $auth->remove($permission);
       }
    }
}
