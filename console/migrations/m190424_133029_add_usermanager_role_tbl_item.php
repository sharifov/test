<?php

use yii\db\Migration;

/**
 * Class m190424_133029_add_usermanager_role_tbl_item
 */
class m190424_133029_add_usermanager_role_tbl_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $um = $auth->createRole('userManager');
        $um->description = 'User Manager';
        $auth->add($um);


        $employee = new \common\models\Employee();
        $employee->username = 'user.manager';
        $employee->email = 'um@zeit.style';
        $employee->acl_rules_activated = false;
        $employee->setPassword('user.manager');
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
        $profile->up_call_type_id = 0;
        $profile->up_updated_dt = date('Y-m-d H:i:s');
        $profile->save(false);


        $authorRole = $auth->getRole('userManager');
        $auth->assign($authorRole, $employee->getId());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $um = $auth->getRole('userManager');
        $auth->remove($um);

        $um = \common\models\Employee::find()->where(['username' => 'user.manager'])->one();
        if($um) {
            $um->delete();
        }
    }
}
