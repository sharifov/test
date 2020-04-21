<?php

use yii\db\Migration;
use common\models\Employee;
use common\models\UserProfile;


/**
 * Class m190306_082636_update_user_project_params_tw_sip_id
 */
class m190306_082636_update_user_project_params_tw_sip_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $users = Employee::find()->orderBy(['id' => SORT_ASC])->all();
        //VarDumper::dump(count($users)); exit;
        $items = [];
        $dtNow = new \DateTime('now');
        $dateFormatNow = $dtNow->format("Y-m-d H:i:s");
        if(count($users)) {
            foreach ($users AS $user) {
                $upps = $user->userProjectParams;
                if(count($upps)) {
                    foreach ($upps AS $upp) {
                        if(!isset($items[$user->id]) && $upp->upp_tw_sip_id && (strlen($upp->upp_tw_sip_id) > 2)) {
                            $items[$user->id] = $upp->upp_tw_sip_id;
                            if(!$user->userProfile) {
                                $userProfile = new UserProfile();
                                $userProfile->up_call_type_id = 2;
                                $userProfile->up_user_id = $user->id;
                                $userProfile->save();
                                $user = Employee::findOne($user->id);
                            }
                            $user->userProfile->up_sip = $upp->upp_tw_sip_id;
                            $user->userProfile->up_updated_dt = $dateFormatNow;
                            $user->userProfile->save();
                        }
                    }
                }
            }
        }
        $this->dropColumn('{{%user_project_params}}', 'upp_tw_sip_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%user_project_params}}', 'upp_tw_sip_id', $this->string(100) );
    }

}
