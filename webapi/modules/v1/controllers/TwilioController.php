<?php
namespace webapi\modules\v1\controllers;

use common\components\jobs\CallQueueJob;
use common\models\Call;
use common\models\CallUserGroup;
use common\models\ClientPhone;
use common\models\DepartmentPhoneProject;
use common\models\DepartmentPhoneProjectUserGroup;
use Twilio\TwiML\VoiceResponse;
use Yii;
use yii\base\Exception;
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
     */
    public function actionRedirectCall()
    {

        //$this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);

        $out = [
//            'dateTime'      => date('Y-m-d H:i:s'),
//            'ip'        => Yii::$app->request->getUserIP(),
            'get'       => Yii::$app->request->get(),
            'post'      => Yii::$app->request->post(),
        ];

        Yii::info(VarDumper::dumpAsString($out), 'info\API:Twilio:RedirectCal');



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


        $id = (int) Yii::$app->request->post('id');

        // "user", "department"
        $type = Yii::$app->request->post('type');

        $callData = Yii::$app->request->post('CallData');
        $sid = $callData['CallSid'] ?? null;

        try {

            if (!$callData) {
                throw new Exception('Params "CallData" is empty', 1);
            }

            if (!$sid) {
                throw new Exception('Params "CallData.CallSid" is empty', 2);
            }

            if (!$id) {
                throw new Exception('Params "id" is empty', 3);
            }

            if (!$type) {
                throw new Exception('Params "type" is empty', 4);
            }

//            if ($sid) {
                //$call = $this->findOrCreateCallByData($callData); //Call::find()->where(['c_call_sid' => $sid])->limit(1)->one();

                //$callSid = $callData['CallSid'] ?? '';
                //$parentCallSid = $callData['ParentCallSid'] ?? '';

            $call = Call::find()->where(['c_call_sid' => $sid])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();
            Yii::info(VarDumper::dumpAsString($callData), 'info\API:Twilio:RedirectCall:callData');

            $responseTwml = new VoiceResponse();

            if ($call) {
                //$call->c_call_status = Call::TW_STATUS_QUEUE;
                //$call->setStatusByTwilioStatus($call->c_call_status);

                $call->setStatusQueue();

                $callUserAccessAny = $call->callUserAccesses; //CallUserAccess::find()->where(['cua_status_id' => [CallUserAccess::STATUS_TYPE_PENDING], 'cua_call_id' => $this->c_id])->all();
                if ($callUserAccessAny) {
                    foreach ($callUserAccessAny as $callAccess) {
                        if ((int)$callAccess->cua_status_id === $callAccess::STATUS_TYPE_PENDING) {
                            $callAccess->delete();
                        }
                    }
                }


                if ($type === 'user') {
                    if ($id) {
                        $call->c_created_user_id = $id;
                        Call::applyCallToAgentAccess($call, $id);
                    }

                    $responseTwml->say('You have been redirected to a call to another agent. Please wait for an answer', [
                        'language' => 'en-US',
                        'voice' => 'alice'
                    ]);

                    $url_music = 'https://talkdeskapp.s3.amazonaws.com/production/audio_messages/folk_hold_music.mp3';
                    $responseTwml->play($url_music, ['loop' => 0]);

                } elseif ($type === 'department') {
                    $call->c_created_user_id = null;
                    $depPhone = DepartmentPhoneProject::findOne($id);
                    if ($depPhone) {

                        if ($call->c_project_id !== $depPhone->dpp_project_id) {
                            $call->c_project_id = $depPhone->dpp_project_id;
                        }
                        $call->c_dep_id = $depPhone->dpp_dep_id;

                        CallUserGroup::deleteAll(['cug_c_id' => $call->c_id]);

                        if ($depPhone->departmentPhoneProjectUserGroups) {

                            foreach ($depPhone->departmentPhoneProjectUserGroups as $dUg) {
                                $callUg = new CallUserGroup();
                                $callUg->cug_c_id = $call->c_id;
                                $callUg->cug_ug_id = $dUg->dug_ug_id;
                                $callUg->save();
                                if (!$callUg->save()) {
                                    Yii::error(VarDumper::dumpAsString($callUg->errors), 'API:Twilio:RedirectCall:CallUserGroup:save');
                                }
                            }
                        }


                        if ($depPhone->dpp_params) {
                            // $ivrStep = 2;
                            $dParams = @json_decode($depPhone->dpp_params, true);
                            $ivrParams = $dParams['ivr'] ?? [];

                           // Your call has been forwarded to the sales department. Please wait for a response from the agent.

                            if ($depPhone->dppDep) {
                                $responseTwml->say('Your call has been forwarded to the ' . strtolower($depPhone->dppDep->dep_name) . ' department. Please wait for an answer',
                                    [
                                        'language' => 'en-US',
                                        'voice' => 'alice'
                                    ]);
                            }

                            if(isset($ivrParams['hold_play']) && $ivrParams['hold_play']) {
                                $responseTwml->play($ivrParams['hold_play'], ['loop' => 0]);
                            }

                            // http://com.twilio.music.classical.s3.amazonaws.com/oldDog_-_endless_goodbye_%28instr.%29.mp3

                        } else {
                            $responseTwml->say('You have been redirected to a call to another department. Please wait for an answer', [
                                'language' => 'en-US',
                                'voice' => 'alice'
                            ]);

                            $url_music = 'https://talkdeskapp.s3.amazonaws.com/production/audio_messages/folk_hold_music.mp3';
                            $responseTwml->play($url_music, ['loop' => 0]);
                        }


                        $job = new CallQueueJob();
                        $job->call_id = $call->c_id;
                        $job->delay = 0;
                        $jobId = Yii::$app->queue_job->delay(7)->priority(80)->push($job);
                    } else {
                        throw new Exception('Not found DepartmentPhoneProject', 10);
                    }


                }

                if (!$call->save()) {
                    Yii::error(VarDumper::dumpAsString($call->errors), 'API:Twilio:RedirectCall:Call:update');
                }
            }
//            } else {
//                Yii::error('Not found CallSid', 'API:Twilio:RedirectCalUser');
//            }


//            $responseTwml->say('You have been redirected to a call to another agent. Please wait for an answer', [
//                'language' => 'en-US',
//                'voice' => 'alice'
//            ]);
//
//            $url_music = 'https://talkdeskapp.s3.amazonaws.com/production/audio_messages/folk_hold_music.mp3';
//            $responseTwml->play($url_music, ['loop' => 0]);

        } catch (\Throwable $throwable) {

            Yii::error('Message: ' . $throwable->getMessage() . ', file: ' . $throwable->getFile().' (' . $throwable->getLine().')', 'API:Twilio:RedirectCall:Throwable');

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

}