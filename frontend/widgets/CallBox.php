<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\widgets;

use common\models\Call;
use common\models\Employee;
use common\models\UserCallStatus;

/**
 * Alert widget renders a message from
 *
 * @author Alexandr <chalpet@gmail.com>
 *
 * JS Example: https://codepen.io/anon/pen/LqZYEo
 *
 */
class CallBox extends \yii\bootstrap\Widget
{
    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $user_id = \Yii::$app->user->id;
        //$newCount = 0; //\common\models\Notifications::findNewCount($user_id);
        //$model = \common\models\Notifications::findNew($user_id);

        /** @var Employee $userModel */
        $userModel = \Yii::$app->user->identity;
        //$sipExist = $sipExist = ($userModel->userProfile->up_sip && strlen($userModel->userProfile->up_sip) > 2); // \common\models\UserProjectParams::find()->where(['upp_user_id' => $user_id])->andWhere(['AND', ['IS NOT', 'upp_tw_sip_id', null], ['!=', 'upp_tw_sip_id', '']])->one();



        //VarDumper::dump($sipExist->attributes, 10, true);

        if(!$userModel) {
            return '';
        }

        if(!$userModel->userProfile || $userModel->userProfile->up_call_type_id == \common\models\UserProfile::CALL_TYPE_OFF) {
            return '';
        }


        $lastCall = Call::find()->where(['c_created_user_id' => $user_id])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();
        $lastCalls = Call::find()->where(['c_created_user_id' => $user_id])->orderBy(['c_id' => SORT_DESC])->limit(5)->all();

        $userCallStatus = UserCallStatus::find()->where(['us_user_id' => $user_id])->orderBy(['us_id' => SORT_DESC])->limit(1)->one();

        return $this->render('call_box', ['lastCall' => $lastCall, 'lastCalls' => $lastCalls, 'userCallStatus' => $userCallStatus]);
    }
}
