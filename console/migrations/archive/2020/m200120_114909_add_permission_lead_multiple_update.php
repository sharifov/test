<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200120_114909_add_permission_lead_multiple_update
 */
class m200120_114909_add_permission_lead_multiple_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $leadsIndexMultipleUpdate = $auth->createPermission('leads/index_MultipleUpdate');
        $auth->add($leadsIndexMultipleUpdate);

        if ($admin = $auth->getRole(Employee::ROLE_ADMIN)) {
            $auth->addChild($admin, $leadsIndexMultipleUpdate);
        }
        if ($supervision = $auth->getRole(Employee::ROLE_SUPERVISION)) {
            $auth->addChild($supervision, $leadsIndexMultipleUpdate);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        if ($leadsIndexMultipleUpdate = $auth->getPermission('leads/index_MultipleUpdate')) {
            $auth->remove($leadsIndexMultipleUpdate);
        }
    }
}
