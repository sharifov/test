<?php
namespace sales\model\user\entity\userCallStatus\events;

use common\models\UserCallStatus;
use sales\model\user\entity\userStatus\UserStatus;
use yii\base\Component;
use yii\helpers\VarDumper;

class UserCallStatusEvents extends Component
{
    public const UPDATE         = 'update';
    public const INSERT         = 'insert';
    public const DELETE         = 'delete';

    /**
     * @param $params
     */
    public function updateUserStatus($params): void
    {

        /**
         * @var $userCallStatusModel UserCallStatus
         */
        $userCallStatusModel = $params->data;

        if ((int) $userCallStatusModel->us_type_id === UserCallStatus::STATUS_TYPE_READY) {
            $callPhoneStatus = true;
        } else {
            $callPhoneStatus = false;
        }

        $userStatus = UserStatus::findOne($userCallStatusModel->us_user_id);

        if (!$userStatus) {
            $userStatus = new UserStatus();
            $userStatus->us_user_id = $userCallStatusModel->us_user_id;
        }

        $userStatus->us_call_phone_status = $callPhoneStatus;

        if (!$userStatus->save()) {
            \Yii::error(VarDumper::dumpAsString($userStatus->errors), 'UserCallStatusEvent:updateUserStatus:UserStatus:save');
        }

        //$sender = $params->sender;

        //$event = new MessageEvent;
        //$event->message = $message;
        //$this->trigger(self::EVENT_MESSAGE_SENT, $event);
        //\Yii::warning(VarDumper::dumpAsString($params), 'UserCallStatusEvents:updateUserStatus');
    }


    /**
     * @param $params
     */
    public function resetCallPhoneStatus($params): void
    {
        /**
         * @var $userCallStatusModel UserCallStatus
         */
        $userCallStatusModel = $params->data;

        $userStatus = UserStatus::findOne($userCallStatusModel->us_user_id);
        $lastUserCallStatus = UserCallStatus::find()->where(['us_user_id' => $userCallStatusModel->us_user_id])->orderBy(['us_id' => SORT_DESC])->limit(1)->one();

        $callPhoneStatus = false;

        if ($lastUserCallStatus && (int) $lastUserCallStatus->us_type_id === UserCallStatus::STATUS_TYPE_READY) {
            $callPhoneStatus = true;
        }

        if (!$userStatus) {
            $userStatus = new UserStatus();
            $userStatus->us_user_id = $userCallStatusModel->us_user_id;
        }

        $userStatus->us_call_phone_status = $callPhoneStatus;

        if (!$userStatus->save()) {
            \Yii::error(VarDumper::dumpAsString($userStatus->errors), 'UserCallStatusEvent:resetCallPhoneStatus:UserStatus:save');
        }
    }

}