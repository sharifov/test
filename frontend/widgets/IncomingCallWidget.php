<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\widgets;

use common\models\Call;
use common\models\CallUserAccess;
use common\models\Employee;
use common\models\Notifications;
use common\models\UserCallStatus;
use Yii;
use yii\helpers\VarDumper;

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

        if(!$userModel) {
            return '';
        }


        $action = \Yii::$app->request->get('act');
        $call_id = \Yii::$app->request->get('call_id');

        if(\Yii::$app->request->isPjax && $action && $call_id) {
            try {
                $call = Call::findOne($call_id);

                if ($call  ) { // && $call->c_call_status === Call::CALL_STATUS_QUEUE && (!$call->c_created_user_id || $call->c_created_user_id == $userModel->id)

                    //VarDumper::dump($action); exit;

                    $callUserAccess = CallUserAccess::find()->where(['cua_user_id' => $userModel->id, 'cua_call_id' => $call->c_id])->one();
                    if ($callUserAccess) {

                        //VarDumper::dump($action); exit;

                        switch ($action) {
                            case 'accept':
                                $this->acceptCall($callUserAccess, $userModel);
                                break;
//                            case 'skip':
//                                $this->skipCall($callUserAccess);
//                                break;
                            case 'busy':
                                $this->busyCall($callUserAccess, $userModel);
                                break;
                        }
                    }
                }
            } catch (\Throwable $exception) {
                \Yii::error($exception->getMessage(), 'IncomingCallWidget:Pjax:Throwable');
            }
        }

        //$lastCall = Call::find()->where(['c_created_user_id' => $userModel->id])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();

        $generalCallUserAccessList = CallUserAccess::find()
            ->innerJoin('call', 'call.c_id = call_user_access.cua_call_id')
            ->where(['cua_user_id' => $userModel->id, 'cua_status_id' => CallUserAccess::STATUS_TYPE_PENDING])
            ->andWhere(['c_created_user_id' => null])
            ->orderBy(['cua_created_dt' => SORT_ASC])
            ->limit(1)
            ->all();

        $directCallUserAccessList = CallUserAccess::find()
            ->innerJoin('call', 'call.c_id = call_user_access.cua_call_id')
            ->where(['cua_user_id' => $userModel->id, 'cua_status_id' => CallUserAccess::STATUS_TYPE_PENDING])
            ->andWhere(['c_created_user_id' => $userModel->id])
            ->orderBy(['cua_created_dt' => SORT_ASC])
            ->limit(10)
            ->all();


        //VarDumper::dump($directCallUserAccessList, 10, true); exit;

        /*if($callUserAccess) {
            $incomingCall = $callUserAccess->cuaCall;
        } else {
            $incomingCall = null;
        }*/

        // $userCallStatus = UserCallStatus::find()->where(['us_user_id' => $user_id])->orderBy(['us_id' => SORT_DESC])->limit(1)->one();
        // $countMissedCalls = Call::find()->where(['c_created_user_id' => $user_id, 'c_call_status' => Call::CALL_STATUS_NO_ANSWER, 'c_call_type_id' => Call::CALL_TYPE_IN, 'c_is_new' => true])->count();
        //$countMissedCalls = 10;

        return $this->render('incoming_call_widget', ['generalCallUserAccessList' => $generalCallUserAccessList, 'directCallUserAccessList' => $directCallUserAccessList]);
    }


    /**
     * @param CallUserAccess $callUserAccess
     * @param Employee $user
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    private function acceptCall(CallUserAccess $callUserAccess, Employee $user): bool
    {
        $callUserAccess->acceptCall();
        if($callUserAccess->update()) {

            if ($call = $callUserAccess->cuaCall) {
                /*$call->c_created_user_id = $user->id;
                $call->c_call_status = Call::CALL_STATUS_IN_PROGRESS;

                if (!$call->update()) {
                    Yii::error(VarDumper::dumpAsString($call->errors), 'IncomingCallWidget:acceptCall:Call:update');
                }*/

                return Call::applyCallToAgent($call, $user->id);
            }

//            $callUserAccessAny = CallUserAccess::find()->where(['cua_status_id' => [CallUserAccess::STATUS_TYPE_PENDING], 'cua_call_id' => $callUserAccess->cua_call_id])->andWhere(['<>', 'cua_user_id', $callUserAccess->cua_user_id])->all();
//            if($callUserAccessAny) {
//                foreach ($callUserAccessAny as $callAccess) {
//                    $callAccess->noAnsweredCall();
//                    if(!$callAccess->update()) {
//                        Yii::error(VarDumper::dumpAsString($callAccess->errors), 'IncomingCallWidget:acceptCall:UserCallStatus:save');
//                    }
//                }
//            }
        }
        return false;
    }

//    /**
//     * @param CallUserAccess $callUserAccess
//     * @throws \Throwable
//     * @throws \yii\db\StaleObjectException
//     */
//    private function skipCall(CallUserAccess $callUserAccess): void
//    {
//        $callUserAccess->skipCall();
//        if(!$callUserAccess->update()) {
//            Yii::error(VarDumper::dumpAsString($callUserAccess->errors), 'IncomingCallWidget:skipCall:UserCallStatus:save');
//        }
//    }


    /**
     * @param CallUserAccess $callUserAccess
     * @param Employee $user
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    private function busyCall(CallUserAccess $callUserAccess, Employee $user): void
    {
        $callUserAccess->busyCall();
        $ucs = new UserCallStatus();
        $ucs->us_type_id = UserCallStatus::STATUS_TYPE_OCCUPIED;
        $ucs->us_user_id = $user->id;
        $ucs->us_created_dt = date('Y-m-d H:i:s');
        if($ucs->save()) {
            $callUserAccess->update();
            Notifications::socket($ucs->us_user_id, null, 'updateUserCallStatus', ['id' => 'ucs'.$ucs->us_id, 'type_id' => $ucs->us_type_id]);
        } else {
            Yii::error(VarDumper::dumpAsString($ucs->errors), 'IncomingCallWidget:busyCall:UserCallStatus:save');
        }
    }
}
