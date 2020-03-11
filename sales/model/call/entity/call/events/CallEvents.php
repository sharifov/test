<?php
namespace sales\model\call\entity\call\events;

use common\models\Call;
use sales\model\user\entity\userStatus\UserStatus;
use yii\base\Component;
use yii\helpers\VarDumper;

class CallEvents extends Component
{
    public const UPDATE         = 'update';
    public const INSERT         = 'insert';
    public const DELETE         = 'delete';
    public const CHANGE_STATUS  = 'change_status';


    /**
     * @param $params
     */
    public function updateUserStatus($params): void
    {
        /**
         * @var $call Call
         */
        $call = $params->data['call'] ?? null;
        $changedAttributes = $params->data['changedAttributes'] ?? [];


        if ($call && $call->c_created_user_id) {

            if ($call->isStatusInProgress() || $call->isStatusRinging()) { // || $call->isStatusQueue()
                //\Yii::warning(VarDumper::dumpAsString($params->data),'CallEvents:updateUserStatus:debug');
                $onCall = true;
            } else {
                $onCall = Call::find()->where(['c_created_user_id' => $call->c_created_user_id, 'c_status_id' => [Call::STATUS_IN_PROGRESS, Call::STATUS_RINGING]])->exists();
            }

            $userStatus = UserStatus::findOne($call->c_created_user_id);

            if (!$userStatus) {
                $userStatus = new UserStatus();
                $userStatus->us_user_id = $call->c_created_user_id;
                $userStatus->us_gl_call_count = 0;
            }

            if ($call->isStatusCompleted()) {
                $userStatus->us_gl_call_count = (int)$userStatus->us_gl_call_count + 1;
            }

            $userStatus->us_is_on_call = $onCall;

            if (!$userStatus->save()) {
                \Yii::error(VarDumper::dumpAsString($userStatus->errors),
                    'CallEvents:updateUserStatus:UserStatus:save');
            }
        }
    }

}