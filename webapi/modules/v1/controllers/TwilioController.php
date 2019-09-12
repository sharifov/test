<?php
namespace webapi\modules\v1\controllers;

use common\models\Call;
use common\models\CallUserGroup;
use common\models\ClientPhone;
use Twilio\TwiML\VoiceResponse;
use Yii;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;


/**
 * Twilio controller
 */
class TwilioController extends ApiBaseNoAuthController
{



    public function init()
    {
        parent::init();
        Yii::$app->user->enableSession = false;
        $this->enableCsrfValidation = false;
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        echo  '<h1>API - Twilio - '.Yii::$app->request->serverName.'</h1> '.date('Y-m-d H:i:s');
        exit;
    }

    public function actionCallback()
    {

        $headers = [];
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        $out = [
            'message'   => 'Server Name: '.Yii::$app->request->serverName,
            'date'      => date('Y-m-d'),
            'time'      => date('H:i:s'),
            'ip'        => Yii::$app->request->getUserIP(),
            'get'       => Yii::$app->request->get(),
            'post'      => Yii::$app->request->post(),
            'files'     => $_FILES,
            'headers'   => $headers
        ];

        Yii::warning(VarDumper::dumpAsString($out), 'Twilio Callback');

    }

    public function actionRequest()
    {

        $headers = [];
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        $out = [
            'message'   => 'Server Name: '.Yii::$app->request->serverName,
            'date'      => date('Y-m-d'),
            'time'      => date('H:i:s'),
            'ip'        => Yii::$app->request->getUserIP(),
            'get'       => Yii::$app->request->get(),
            'post'      => Yii::$app->request->post(),
            'files'     => $_FILES,
            'headers'   => $headers
        ];

        Yii::warning(VarDumper::dumpAsString($out), 'Twilio Request');

        header("Content-type: text/xml");

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<Response><Dial callerId="sip:alex.connor@kivork.sip.us1.twilio.com" record="true">
            <Number>'. Yii::$app->request->get('phone') .'</Number>
        </Dial>
</Response>';

        /*      $xml = '<Response><Dial timeout="10" record="true">
                    <Sip>'. $tosip .'</Sip>
                </Dial>
        </Response>';*/
        echo  $xml; exit;

    }


    public function actionFallback()
    {


        $headers = [];
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        $out = [
            'message'   => 'Server Name: '.Yii::$app->request->serverName,
            'date'      => date('Y-m-d'),
            'time'      => date('H:i:s'),
            'ip'        => Yii::$app->request->getUserIP(),
            'get'       => Yii::$app->request->get(),
            'post'      => Yii::$app->request->post(),
            'files'     => $_FILES,
            'headers'   => $headers
        ];

        Yii::warning(VarDumper::dumpAsString($out), 'Twilio Fallback');

    }


    /**
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionRedirectCallUser()
    {

        //$this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);

//        $out = [
//            'dateTime'      => date('Y-m-d H:i:s'),
//            'ip'        => Yii::$app->request->getUserIP(),
//            'get'       => Yii::$app->request->get(),
//            'post'      => Yii::$app->request->post(),
//        ];
//
//        Yii::info(VarDumper::dumpAsString($out), 'info\API:Twilio:RedirectCalUser');



//        [
//            'ApiVersion' => '2010-04-01'
//        'Called' => 'client:seller238'
//        'ParentCallSid' => 'CA4119e45239f8ecf9114cd5cd7d1c7f93'
//        'CallStatus' => 'in-progress'
//        'From' => '+37379731662'
//        'CallerCountry' => 'MD'
//        'Direction' => 'outbound-dial'
//        'AccountSid' => 'AC10f3c74efba7b492cbd7dca86077736c'
//        'CallerCity' => ''
//        'CalledVia' => '+16692011257'
//        'CallerState' => ''
//        'Caller' => '+37379731662'
//        'FromCountry' => 'MD'
//        'FromCity' => ''
//        'CallSid' => 'CA9f0c771e00ba81f6f1130f1a205f9b6e'
//        'To' => 'client:seller238'
//        'ForwardedFrom' => '+16692011257'
//        'FromZip' => ''
//        'CallerZip' => ''
//        'FromState' => ''
//    ]


        $userId = Yii::$app->request->post('user_id');
        $callData = Yii::$app->request->post('CallData');
        $sid = $callData['CallSid'] ?? null;

        if (!$sid) {
            throw new BadRequestHttpException('Params "CALL SID" is empty', 1);
        }

        try {
//            if ($sid) {
                //$call = $this->findOrCreateCallByData($callData); //Call::find()->where(['c_call_sid' => $sid])->limit(1)->one();

                //$callSid = $callData['CallSid'] ?? '';
                //$parentCallSid = $callData['ParentCallSid'] ?? '';

                $call = Call::find()->where(['c_call_sid' => $sid])->limit(1)->one();


                Yii::info(VarDumper::dumpAsString($callData), 'info\API:Twilio:RedirectCalUser:callData');

                if ($call) {
                    $call->c_call_status = Call::CALL_STATUS_QUEUE;
                    $call->setStatusByTwilioStatus($call->c_call_status);

                    if ($userId) {
                        $call->c_created_user_id = (int) $userId;
                    }

                    $callUserAccessAny = $call->callUserAccesses; //CallUserAccess::find()->where(['cua_status_id' => [CallUserAccess::STATUS_TYPE_PENDING], 'cua_call_id' => $this->c_id])->all();
                    if ($callUserAccessAny) {
                        foreach ($callUserAccessAny as $callAccess) {
                            if ((int) $callAccess->cua_status_id === $callAccess::STATUS_TYPE_PENDING) {
                                $callAccess->delete();
                            }
                        }
                    }

                    Call::applyCallToAgentAccess($call, $userId);

                    if (!$call->save()) {
                        Yii::error(VarDumper::dumpAsString($call->errors), 'API:Twilio:RedirectCalUser:Call:update');
                    }
                }
//            } else {
//                Yii::error('Not found CallSid', 'API:Twilio:RedirectCalUser');
//            }

            $responseTwml = new VoiceResponse();
            $responseTwml->say('You have been redirected to a call to another agent. Please wait for an answer', [
                'language' => 'en-US',
                'voice' => 'alice'
            ]);
            $url_music = 'https://talkdeskapp.s3.amazonaws.com/production/audio_messages/folk_hold_music.mp3';
            $responseTwml->play($url_music, ['loop' => 0]);

        } catch (\Throwable $throwable) {

            Yii::error($throwable->getTraceAsString(), 'API:Twilio:RedirectCalUser:Throwable');

            $responseTwml = new VoiceResponse();
            $responseTwml->say('Sorry, communication error', [
                'language' => 'en-US',
                'voice' => 'alice'
            ]);
        }
        $responseData['responseTwml'] = (string) $responseTwml;

        $apiLog->endApiLog($responseData);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $responseData;
    }


//    protected function findOrCreateCallByData(array $callData): Call
//    {
//        $call = null;
//        $parentCall = null;
//        $clientPhone = null;
//
//        if (isset($callData['From']) && $callData['From']) {
//            $clientPhoneNumber = $callData['From'];
//            if ($clientPhoneNumber) {
//                $clientPhone = ClientPhone::find()->where(['phone' => $clientPhoneNumber])->orderBy(['id' => SORT_DESC])->limit(1)->one();
//            }
//        }
//
//        $callSid = $callData['CallSid'] ?? '';
//        $parentCallSid = $callData['ParentCallSid'] ?? '';
//
//        if ($callSid) {
//            $call = Call::find()->where(['c_call_sid' => $callSid])->limit(1)->one();
//        }
//
//        if ($parentCallSid) {
//            $parentCall = Call::find()->where(['c_call_sid' => $parentCallSid])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();
//        }
//
//
//        if (!$call) {
//
//            $call = new Call();
//            $call->c_call_sid = $callData['CallSid'] ?? null;
//            $call->c_parent_call_sid = $callData['ParentCallSid'] ?? null;
//            $call->c_com_call_id = $callData['c_com_call_id'] ?? null;
//            $call->c_call_type_id = Call::CALL_TYPE_IN;
//
//
//            if ($parentCall) {
//                $call->c_parent_id = $parentCall->c_id;
//                $call->c_project_id = $parentCall->c_project_id;
//                $call->c_dep_id = $parentCall->c_dep_id;
//                $call->c_source_type_id = $parentCall->c_source_type_id;
//
//
//                $call->c_lead_id = $parentCall->c_lead_id;
//                $call->c_case_id = $parentCall->c_case_id;
//                $call->c_client_id = $parentCall->c_client_id;
//
//                $call->c_created_user_id = $parentCall->c_created_user_id;
//
//                /*if ($parentCall->c_lead_id) {
//
//                }*/
//
//                if ($parentCall->callUserGroups && !$call->callUserGroups) {
//                    foreach ($parentCall->callUserGroups as $cugItem) {
//                        $cug = new CallUserGroup();
//                        $cug->cug_ug_id = $cugItem->cug_ug_id;
//                        $cug->cug_c_id = $call->c_id;
//                        if (!$cug->save()) {
//                            \Yii::error(VarDumper::dumpAsString($cug->errors), 'API:CommunicationController:findOrCreateCall:CallUserGroup:save');
//                        }
//                    }
//                }
//                //$call->c_u_id = $parentCall->c_dep_id;
//            }
//
////            if ($call_project_id) {
////                $call->c_project_id = $call_project_id;
////            }
////            if ($call_dep_id) {
////                $call->c_dep_id = $call_dep_id;
////            }
//
//            $call->c_is_new = true;
//            $call->c_created_dt = date('Y-m-d H:i:s');
//            $call->c_from = $callData['From'];
//            $call->c_to = $callData['To']; //Called
//            $call->c_created_user_id = null;
//
//            /*if ($clientPhone && $clientPhone->client_id) {
//                $call->c_client_id = $clientPhone->client_id;
//            }*/
//
////            if ($call->c_dep_id === Department::DEPARTMENT_SALES) {
////                /*$lead = Lead2::findLastLeadByClientPhone($call->c_from, $call->c_project_id);
////                if ($lead) {
////                    $call->c_lead_id = $lead->id;
////                }*////
////            } elseif ($call->c_dep_id === Department::DEPARTMENT_EXCHANGE || $call->c_dep_id === Department::DEPARTMENT_SUPPORT) {
////
////            }
//
//            /*if (!$call->save()) {
//                \Yii::error(VarDumper::dumpAsString($call->errors), 'API:CommunicationController:findOrCreateCallByData:Call:save');
//                throw new \Exception('findOrCreateCallByData: Can not save call in db', 1);
//            }*/
//        }
//
//
//
//        if ($call->isFailed() || $call->isNoAnswer() || $call->isBusy() || $call->isCanceled()) {
//            $call->c_call_status = $callData['CallStatus'];
//
//        } else {
//
//            $call->c_call_status = $callData['CallStatus'];
//            $statusId = $call->setStatusByTwilioStatus($call->c_call_status);
//            $call->c_status_id = $statusId;
//        }
//
//
//        $agentId = null;
//
//        if (isset($callData['Called']) && $callData['Called']) {
//            if(strpos($callData['Called'], 'client:seller') !== false) {
//                $agentId = (int) str_replace('client:seller', '', $callData['Called']);
//            }
//        }
//
//        if (!$agentId) {
//            if (isset($callData['c_user_id']) && $callData['c_user_id']) {
//                $agentId = (int) $callData['c_user_id'];
//            }
//        }
//
//        if ($agentId) {
//            $call->c_created_user_id = $agentId;
//        }
//
//        if (isset($callData['SequenceNumber'])) {
//            $call->c_sequence_number = $callData['SequenceNumber'] ?? 0;
//        }
//
//        if (isset($callData['CallDuration'])) {
//            $call->c_call_duration = (int) $callData['CallDuration'];
//        }
//
//        if (!$call->c_forwarded_from && isset($callData['ForwardedFrom'])) {
//            $call->c_forwarded_from = $callData['ForwardedFrom'];
//        }
//
//        return $call;
//    }

}