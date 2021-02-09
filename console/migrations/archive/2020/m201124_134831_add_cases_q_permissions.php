<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m201124_134831_add_cases_q_permissions
 */
class m201124_134831_add_cases_q_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $processingAll = $auth->createPermission('cases-q/processing/list/all');
        $auth->add($processingAll);
        $processingOwner = $auth->createPermission('cases-q/processing/list/owner');
        $auth->add($processingOwner);
        $processingGroup = $auth->createPermission('cases-q/processing/list/group');
        $auth->add($processingGroup);
        $processingEmpty = $auth->createPermission('cases-q/processing/list/empty');
        $auth->add($processingEmpty);

        $solvedAll = $auth->createPermission('cases-q/solved/list/all');
        $auth->add($solvedAll);
        $solvedOwner = $auth->createPermission('cases-q/solved/list/owner');
        $auth->add($solvedOwner);
        $solvedGroup = $auth->createPermission('cases-q/solved/list/group');
        $auth->add($solvedGroup);
        $solvedEmpty = $auth->createPermission('cases-q/solved/list/empty');
        $auth->add($solvedEmpty);

        $trashAll = $auth->createPermission('cases-q/trash/list/all');
        $auth->add($trashAll);
        $trashOwner = $auth->createPermission('cases-q/trash/list/owner');
        $auth->add($trashOwner);
        $trashGroup = $auth->createPermission('cases-q/trash/list/group');
        $auth->add($trashGroup);
        $trashEmpty = $auth->createPermission('cases-q/trash/list/empty');
        $auth->add($trashEmpty);

        $rolesAgents = [Employee::ROLE_SUP_AGENT, Employee::ROLE_EX_AGENT];
        foreach ($rolesAgents as $rolesAgent) {
            if ($role = $auth->getRole($rolesAgent)) {
                $auth->addChild($role, $processingOwner);
                $auth->addChild($role, $solvedOwner);
                $auth->addChild($role, $trashOwner);
            }
        }

        $rolesSupervisors = [Employee::ROLE_EX_SUPER, Employee::ROLE_SUP_SUPER];
        foreach ($rolesSupervisors as $rolesSupervisor) {
            if ($role = $auth->getRole($rolesSupervisor)) {
                $auth->addChild($role, $processingGroup);
                $auth->addChild($role, $solvedGroup);
                $auth->addChild($role, $trashGroup);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $permissions = [
            'cases-q/processing/list/all',
            'cases-q/processing/list/owner',
            'cases-q/processing/list/group',
            'cases-q/processing/list/empty',
            'cases-q/solved/list/all',
            'cases-q/solved/list/owner',
            'cases-q/solved/list/group',
            'cases-q/solved/list/empty',
            'cases-q/trash/list/all',
            'cases-q/trash/list/owner',
            'cases-q/trash/list/group',
            'cases-q/trash/list/empty',
        ];

        foreach ($permissions as $permission) {
            if ($p = $auth->getPermission($permission)) {
                $auth->remove($p);
            }
        }
    }
}
