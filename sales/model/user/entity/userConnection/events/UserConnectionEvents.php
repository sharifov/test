<?php
namespace sales\model\user\entity\userConnection\events;

use common\models\CallUserAccess;
use common\models\UserConnection;
use common\models\UserOnline;
use sales\model\user\entity\userStatus\UserStatus;
use yii\base\Component;
use yii\helpers\VarDumper;

class UserConnectionEvents extends Component
{
    public const UPDATE         = 'update';
    public const INSERT         = 'insert';
    public const DELETE         = 'delete';

    /**
     * @param $params
     */
    public function insertUserOnline($params): void
    {

        /**
         * @var $userConnection UserConnection
         */
        $userConnection = $params->data;

        if ($userConnection->uc_user_id) {
            $exist = UserOnline::find()->where(['uo_user_id' => $userConnection->uc_user_id])->exists();

            if (!$exist) {
                $uo = new UserOnline();
                $uo->uo_user_id = $userConnection->uc_user_id;
                if (!$uo->save()) {
                    \Yii::error(VarDumper::dumpAsString($uo->errors), 'UserConnectionEvents:insertUserOnline:UserOnline:save');
                }
            }
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
    public function deleteUserOnline($params): void
    {
        /**
         * @var $userConnection UserConnection
         */
        $userConnection = $params->data;

        if ($userConnection->uc_user_id) {
            $exist = UserConnection::find()->where(['uc_user_id' => $userConnection->uc_user_id])->exists();
            if (!$exist) {
                $uo = UserOnline::find()->where(['uo_user_id' => $userConnection->uc_user_id])->limit(1)->one();
                if ($uo) {
                    $uo->delete();
                }
            }
        }
    }

}