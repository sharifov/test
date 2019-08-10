<?php

use yii\db\Migration;

/**
 * Class m190809_104922_add_support_role_tbl_auth_item
 */
class m190809_104922_add_support_role_tbl_auth_item extends Migration
{

    public $routes = [
        '/site/index',
        '/lead/create',
        '/lead/index',
        '/notifications/list',
        '/email/list',
        '/sms/list',
        '/call/list',
        '/sales/search',
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $auth = Yii::$app->authManager;

        // add "admin" and "user" role
        $role = $auth->createRole('support');
        $role->description = 'Customer Support';
        $auth->add($role);

        foreach ($this->routes as $route) {

            $permission = $auth->getPermission($route);
            if(!$permission) {
                $permission = $auth->createPermission($route);
                $auth->add($permission);
            }




            $auth->addChild($auth->getRole('support'), $permission);
            //$auth->addChild($auth->getRole('supervisor'), $permission);
        }

        $permission = $auth->createPermission('/sale/search');
        $auth->addChild($auth->getRole('admin'), $permission);

        $employee = new \common\models\Employee();
        $employee->username = 'support.test';
        $employee->email = 'support.test@techork.com';
        $employee->acl_rules_activated = false;
        $employee->setPassword('support.test');
        $employee->generateAuthKey();
        $employee->save(false);



        $up = new \common\models\UserParams();
        $up->up_user_id = $employee->getId();
        $up->up_updated_dt = date('Y-m-d H:i:s');
        $up->up_timezone = 'Europe/Chisinau';
        $up->up_work_minutes = 480;
        $up->up_work_start_tm = '13:00';
        $up->save(false);

        $profile = new \common\models\UserProfile();
        $profile->up_user_id = $employee->getId();
        $profile->up_call_type_id = \common\models\UserProfile::CALL_TYPE_WEB;
        $profile->up_updated_dt = date('Y-m-d H:i:s');
        $profile->save(false);


        $authorRole = $auth->getRole('support');
        $auth->assign($authorRole, $employee->getId());

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
        $qa = $auth->getRole('support');
        $auth->remove($qa);

        $user = \common\models\Employee::find()->where(['username' => 'support.test'])->one();
        if($user) {
            $user->delete();
        }


        /*foreach ($this->routes as $route) {
            if ($permission = $auth->getPermission($route)) {
                $auth->remove($permission);
            }
        }*/

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

    }
}
