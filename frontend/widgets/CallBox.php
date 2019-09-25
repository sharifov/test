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

    private static $instance;

    /**
     * Returns *CallBox* instance of this class.
     *
     * @return CallBox The *CallBox* instance.
     */
    public static function getInstance(): CallBox
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     *
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @return string
     */
    public function run()
    {
        $user_id = \Yii::$app->user->id;
        /** @var Employee $userModel */
        $userModel = \Yii::$app->user->identity;

        if(!$userModel) {
            return '';
        }

        if(!$userModel->userProfile || (int) $userModel->userProfile->up_call_type_id === \common\models\UserProfile::CALL_TYPE_OFF) {
            return '';
        }

        $lastCall = Call::find()->where(['c_created_user_id' => $user_id])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();
        $userCallStatus = UserCallStatus::find()->where(['us_user_id' => $user_id])->orderBy(['us_id' => SORT_DESC])->limit(1)->one();
        $countMissedCalls = Call::find()->where(['c_created_user_id' => $user_id, 'c_status_id' => Call::STATUS_NO_ANSWER, 'c_call_type_id' => Call::CALL_TYPE_IN, 'c_is_new' => true])->count();
        //$countMissedCalls = 10;
        return $this->render('call_box', ['lastCall' => $lastCall, 'userCallStatus' => $userCallStatus, 'countMissedCalls' => $countMissedCalls]);
    }
}
