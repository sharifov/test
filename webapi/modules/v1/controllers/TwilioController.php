<?php
namespace webapi\modules\v1\controllers;

use common\models\Call;
use Twilio\TwiML\VoiceResponse;
use Yii;
use yii\helpers\VarDumper;


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

        $this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);

        $out = [
            'dateTime'      => date('Y-m-d H:i:s'),
            'ip'        => Yii::$app->request->getUserIP(),
            'get'       => Yii::$app->request->get(),
            'post'      => Yii::$app->request->post(),
        ];

        Yii::info(VarDumper::dumpAsString($out), 'info\API:Twilio:RedirectCalUser');


//        {
//            "account_sid": "ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
//  "annotation": null,
//  "answered_by": null,
//  "api_version": "2010-04-01",
//  "caller_name": null,
//  "date_created": "Tue, 31 Aug 2010 20:36:28 +0000",
//  "date_updated": "Tue, 31 Aug 2010 20:36:44 +0000",
//  "direction": "inbound",
//  "duration": "15",
//  "end_time": "Tue, 31 Aug 2010 20:36:44 +0000",
//  "forwarded_from": "+141586753093",
//  "from": "+14158675308",
//  "from_formatted": "(415) 867-5308",
//  "group_sid": null,
//  "parent_call_sid": null,
//  "phone_number_sid": "PNXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
//  "price": -0.03000,
//  "price_unit": "USD",
//  "sid": "CAXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
//  "start_time": "Tue, 31 Aug 2010 20:36:29 +0000",
//  "status": "completed",
//  "subresource_uris": {
//            "notifications": "/2010-04-01/Accounts/ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Calls/CAXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Notifications.json",
//    "recordings": "/2010-04-01/Accounts/ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Calls/CAXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Recordings.json",
//    "feedback": "/2010-04-01/Accounts/ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Calls/CAXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Feedback.json",
//    "feedback_summaries": "/2010-04-01/Accounts/ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Calls/FeedbackSummary.json"
//  },
//  "to": "+14158675309",
//  "to_formatted": "(415) 867-5309",
//  "uri": "/2010-04-01/Accounts/ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Calls/CAXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX.json"
//}


        $userId = Yii::$app->request->get('user_id');
        $sid = Yii::$app->request->get('sid');

        try {
            if ($sid) {
                $call = Call::find()->where(['c_call_sid' => $sid])->limit(1)->one();
                if ($call) {
                    $call->c_call_status = Call::CALL_STATUS_QUEUE;
                    if ($userId) {
                        $call->c_created_user_id = (int)$userId;
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
            }

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
        $responseData['responseTwml'] = $responseTwml;

        $apiLog->endApiLog($responseData);

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/xml');
        return $responseTwml;
    }

}