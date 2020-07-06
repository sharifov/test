<?php

use yii\db\Migration;
use common\models\Employee;

/**
 * Class m200702_054932_add_permission_lead_view_client_info_subscribe
 */
class m200702_054932_add_permission_lead_view_client_info_subscribe extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $admin = $auth->getRole(Employee::ROLE_ADMIN);
        $superAdmin = $auth->getRole(Employee::ROLE_SUPER_ADMIN);

        $checkList = $auth->createPermission('client-project/subscribe-client-ajax');
        $checkList->description = 'Lead View Subscribe Client';
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

        if ($permission = $auth->getPermission('client-project/subscribe-client-ajax')) {
            $auth->remove($permission);
        }
    }
}
