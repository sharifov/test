<?php

use yii\db\Migration;
use common\models\Employee;

/**
 * Class m200701_095636_add_permissions_lead_view_client_info_unsubscribe
 */
class m200701_095636_add_permissions_lead_view_client_info_unsubscribe extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $admin = $auth->getRole(Employee::ROLE_ADMIN);
        $superAdmin = $auth->getRole(Employee::ROLE_SUPER_ADMIN);

        $checkList = $auth->createPermission('client-project/unsubscribe-client-ajax');
        $checkList->description = 'Lead View Unsubscribe Client';
        $auth->add($checkList);
        $auth->addChild($admin, $checkList);
        $auth->addChild($superAdmin, $checkList);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        if ($permission = $auth->getPermission('client-project/unsubscribe-client-ajax')) {
            $auth->remove($permission);
        }
    }
}
