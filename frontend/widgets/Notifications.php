<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\widgets;

use common\models\Call;
use common\models\Email;
use common\models\Sms;
use yii\helpers\VarDumper;

/**
 * Alert widget renders a message from
 *
 * @author Alexandr <chalpet@gmail.com>
 */
class Notifications extends \yii\bootstrap\Widget
{
    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $user_id = \Yii::$app->user->id;




        $newCount = \common\models\Notifications::findNewCount($user_id);
        $model = \common\models\Notifications::findNew($user_id);

        $newCallCount = Call::find()->where(['c_created_user_id' => $user_id, 'c_call_type_id' => Call::CALL_TYPE_IN, 'c_is_new' => true])->count();
        $newSmsCount = Sms::find()->where(['s_created_user_id' => $user_id, 's_type_id' => Sms::TYPE_INBOX, 's_is_new' => true])->count();
        $newEmailCount = Email::find()->where(['e_created_user_id' => $user_id, 'e_type_id' => Email::TYPE_INBOX, 'e_is_new' => true])->count();

        //if($newEmailCount > 0) {

        $this->view->registerJs("$('#call-inbox-queue').text(" . ($newCallCount ?: '') . ");", \yii\web\View::POS_READY);
        $this->view->registerJs("$('#sms-inbox-queue').text(" . ($newSmsCount ?: '') . ");", \yii\web\View::POS_READY);
        $this->view->registerJs("$('#email-inbox-queue').text(" . ($newEmailCount ?: '') . ");", \yii\web\View::POS_READY);



        /*} else {

        }*/

        return $this->render('notifications', ['model' => $model, 'newCount' => $newCount]);
    }
}
