<?php
namespace webapi\modules\v1\controllers;

use common\models\Call;
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
     * @return VoiceResponse
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionRedirectCallUser()
    {

        //$this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);

        $out = [
            'dateTime'      => date('Y-m-d H:i:s'),
            'ip'        => Yii::$app->request->getUserIP(),
            'get'       => Yii::$app->request->get(),
            'post'      => Yii::$app->request->post(),
        ];

        Yii::info(VarDumper::dumpAsString($out), 'info\API:Twilio:RedirectCalUser');



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


        $userId = Yii::$app->request->get('user_id');
        $sid = Yii::$app->request->post('CallSid');
        $post = Yii::$app->request->post();

        if(!$sid) {
            throw new BadRequestHttpException('Params "CALL SID" is empty', 1);
        }

        try {
//            if ($sid) {
                $call = Call::find()->where(['c_call_sid' => $sid])->limit(1)->one();

                if ($call) {
                    $call->c_call_status = Call::CALL_STATUS_QUEUE;
                    if ($userId) {
                        $call->c_created_user_id = (int) $userId;
                    }

                    if (isset($post['AccountSid']) && !$call->c_account_sid) {
                        $call->c_account_sid = $post['AccountSid'];
                    }

                    if (isset($post['Direction']) && !$call->c_direction) {
                        $call->c_direction = $post['Direction'];
                    }

                    if (isset($post['ParentCallSid']) && !$call->c_parent_call_sid) {
                        $call->c_parent_call_sid = $post['ParentCallSid'];
                    }

                    if (isset($post['ApiVersion']) && !$call->c_api_version) {
                        $call->c_api_version = $post['ApiVersion'];
                    }

                    if (isset($post['ForwardedFrom']) && !$call->c_forwarded_from) {
                        $call->c_forwarded_from = $post['ForwardedFrom'];
                    }

                    $callUserAccessAny = $call->callUserAccesses; //CallUserAccess::find()->where(['cua_status_id' => [CallUserAccess::STATUS_TYPE_PENDING], 'cua_call_id' => $this->c_id])->all();
                    if ($callUserAccessAny) {
                        foreach ($callUserAccessAny as $callAccess) {
                            $callAccess->delete();
                        }
                    }

                    Call::applyCallToAgentAccess($call, $userId);

                    if (!$call->update()) {
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
        $responseData['request'] = $out;
        $responseData['responseTwml'] = (string) $responseTwml;

        $apiLog->endApiLog($responseData);

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/xml');
        return $responseTwml;
    }

}