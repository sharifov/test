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
class IncomingCallWidget extends \yii\bootstrap\Widget
{

    private static $instance;

    /**
     * Returns *IncomingCallWidget* instance of this class.
     *
     * @return IncomingCallWidget The *IncomingCallWidget* instance.
     */
    public static function getInstance(): IncomingCallWidget
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        /** @var Employee $userModel */
        $userModel = \Yii::$app->user->identity;
        //$sipExist = $sipExist = ($userModel->userProfile->up_sip && strlen($userModel->userProfile->up_sip) > 2); // \common\models\UserProjectParams::find()->where(['upp_user_id' => $user_id])->andWhere(['AND', ['IS NOT', 'upp_tw_sip_id', null], ['!=', 'upp_tw_sip_id', '']])->one();

        //VarDumper::dump($sipExist->attributes, 10, true);

        if(!$userModel) {
            return '';
        }


        // $lastCall = Call::find()->where(['c_created_user_id' => $user_id])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();
        // $userCallStatus = UserCallStatus::find()->where(['us_user_id' => $user_id])->orderBy(['us_id' => SORT_DESC])->limit(1)->one();
        // $countMissedCalls = Call::find()->where(['c_created_user_id' => $user_id, 'c_call_status' => Call::CALL_STATUS_NO_ANSWER, 'c_call_type_id' => Call::CALL_TYPE_IN, 'c_is_new' => true])->count();
        //$countMissedCalls = 10;


        return $this->render('incoming_call_widget', []);
    }
}
