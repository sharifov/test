<?php
namespace sales\model\call\entity\callUserAccess\events;

use common\models\CallUserAccess;
use sales\model\user\entity\userStatus\UserStatus;
use yii\base\Component;
use yii\helpers\VarDumper;

class CallUserAccessEvents extends Component
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
         * @var $callUserAccess CallUserAccess
         */
        $callUserAccess = $params->data;

        if ((int) $callUserAccess->cua_status_id === CallUserAccess::STATUS_TYPE_PENDING) {
            $callAccess = true;
        } else {
            $callAccess = false;
        }

        $userStatus = UserStatus::findOne($callUserAccess->cua_user_id);

        if (!$userStatus) {
            $userStatus = new UserStatus();
            $userStatus->us_user_id = $callUserAccess->cua_user_id;
        }

        $userStatus->us_has_call_access = $callAccess;

        if (!$userStatus->save()) {
            \Yii::error(VarDumper::dumpAsString($userStatus->errors), 'CallUserAccessEvents:updateUserStatus:UserStatus:save');
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
    public function resetHasCallAccess($params): void
    {
        /**
         * @var $callUserAccess CallUserAccess
         */
        $callUserAccess = $params->data;

//        \Yii::warning(VarDumper::dumpAsString($callUserAccess), 'resetHasCallAccess');

        $userStatus = UserStatus::findOne($callUserAccess->cua_user_id);

        $callAccess = CallUserAccess::find()->where(['cua_user_id' => $callUserAccess->cua_user_id, 'cua_status_id' => CallUserAccess::STATUS_TYPE_PENDING])->exists();

        if (!$userStatus) {
            $userStatus = new UserStatus();
            $userStatus->us_user_id = $callUserAccess->cua_user_id;
        }

        $userStatus->us_has_call_access = $callAccess;

        if (!$userStatus->save()) {
            \Yii::error(VarDumper::dumpAsString($userStatus->errors), 'CallUserAccessEvents:resetHasCallAccess:UserStatus:save');
        }
    }

}