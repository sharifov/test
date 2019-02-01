<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\widgets;

use common\models\Call;
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
        //$user_id = \Yii::$app->user->id;
        $newCount = 0; //\common\models\Notifications::findNewCount($user_id);
        //$model = \common\models\Notifications::findNew($user_id);

        $lastCall = Call::find()->where(['c_created_user_id' => \Yii::$app->user->id])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();
        $lastCalls = Call::find()->where(['c_created_user_id' => \Yii::$app->user->id])->orderBy(['c_id' => SORT_DESC])->limit(5)->all();

        $userCallStatus = UserCallStatus::find()->where(['us_user_id' => \Yii::$app->user->id])->orderBy(['us_id' => SORT_DESC])->limit(1)->one();

        return $this->render('call_box', ['lastCall' => $lastCall, 'lastCalls' => $lastCalls, 'userCallStatus' => $userCallStatus]);
    }
}
