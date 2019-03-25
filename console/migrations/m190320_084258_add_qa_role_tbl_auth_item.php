<?php

use yii\db\Migration;

/**
 * Class m190320_084258_add_qa_role_tbl_auth_item
 */
class m190320_084258_add_qa_role_tbl_auth_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $auth = Yii::$app->authManager;

        // add "admin" and "user" role
        $qa = $auth->createRole('qa');
        $qa->description = 'Quality Assurance';
        $auth->add($qa);


        $employee = new \common\models\Employee();
        $employee->username = 'qa.test';
        $employee->email = 'qa@zeit.style';
        $employee->acl_rules_activated = false;
        $employee->setPassword('qa.test');
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


        $authorRole = $auth->getRole('qa');
        $auth->assign($authorRole, $employee->getId());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $qa = $auth->getRole('qa');
        $auth->remove($qa);

        $qa = \common\models\Employee::find()->where(['username' => 'qa.test'])->one();
        if($qa) {
            $qa->delete();
        }
    }


}
