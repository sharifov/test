<?php

namespace webapi\modules\v1\controllers;

use common\components\jobs\CallQueueJob;
use common\components\TwilioClient;
use common\models\Call;
use common\models\CallUserGroup;
use common\models\ClientPhone;
use common\models\Conference;
use common\models\DepartmentPhoneProject;
use common\models\Sms;
use modules\twilio\src\entities\conferenceLog\ConferenceLog;
use modules\twilio\src\services\sms\SmsCommunicationService;
use src\helpers\app\AppHelper;
use src\model\call\services\QueueLongTimeNotificationJobCreator;
use src\model\call\services\RepeatMessageCallJobCreator;
use src\model\department\departmentPhoneProject\entity\params\QueueLongTimeNotificationParams;
use src\model\user\entity\userStatus\UserStatus;
use src\model\phoneList\entity\PhoneList;
use src\model\voip\phoneDevice\device\VoipDevice;
use Twilio\TwiML\MessagingResponse;
use Twilio\TwiML\VoiceResponse;
use webapi\src\services\communication\CommunicationService;
use webapi\src\services\communication\RequestDataDTO;
use Yii;
use yii\base\Exception;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\web\BadRequestHttpException;

/**
 * Twilio controller
 *
 * @property CommunicationService $communicationService
 * @property SmsCommunicationService $smsCommunicationService
 * @property string $voiceStatusCallbackUrl
 * @property string $recordingStatusCallbackUrl
 * @property string $host
 */
class TwilioController extends ApiBaseNoAuthController
{
    private string $voiceStatusCallbackUrl;
    private string $recordingStatusCallbackUrl;
    private string $host;

    /**
     * @var CommunicationService
     */
    private CommunicationService $communicationService;

    /**
     * @var SmsCommunicationService
     */
    private SmsCommunicationService $smsCommunicationService;

    public function __construct(
        $id,
        $module,
        CommunicationService $communicationService,
        SmsCommunicationService $smsCommunicationService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->communicationService = $communicationService;
        $this->smsCommunicationService = $smsCommunicationService;
    }

    public function init(): void
    {
        parent::init();
        Yii::$app->user->enableSession = false;
        $this->enableCsrfValidation = false;
        $schemeHost = Yii::$app->params['scheme_host'] ?? 'https';

        if (isset(Yii::$app->params['autodetect_host']) && Yii::$app->communication->host && Yii::$app->params['autodetect_host'] === false) {
            $serverHost = Yii::$app->communication->host;
        } else {
            $serverHost = $_SERVER['HTTP_HOST'];
        }
        $host = $schemeHost . '://' . $serverHost;
        $this->host = $host;
        $this->voiceStatusCallbackUrl = $host . '/v1/twilio/voice-status-callback';
        $this->recordingStatusCallbackUrl = $host . '/v1/twilio/recording-status-callback';
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    /**
     * @api {get, post} /v1/twilio/index Twilio index action
     * @apiVersion 0.1.0
     * @apiName index
     * @apiGroup Twilio
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  <h1>API - Telegram - 127.0.0.1</h1> 2022-06-23 15:01:52
     *
     *
     * @return array
     */
    public function actionIndex()
    {
        echo  '<h1>API - Twilio - ' . Yii::$app->request->serverName . '</h1> ' . date('Y-m-d H:i:s');
        exit;
    }


    /**
     * @api {get, post} /v1/twilio/callback Twilio callback action
     * @apiVersion 0.1.0
     * @apiName Callback
     * @apiGroup Twilio
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  null
     *
     *
     * @return array
     */
    public function actionCallback()
    {

        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        $out = [
            'message'   => 'Server Name: ' . Yii::$app->request->serverName,
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

    /**
     * @api {post} /v1/twilio/messaging-request Messaging Request action
     * @apiVersion 0.1.0
     * @apiName MessagingRequest
     * @apiGroup Twilio
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  <?xml version="1.0" encoding="UTF-8"?>
     *  <Response/>
     *
     *
     * @return MessagingResponse
     */
    public function actionMessagingRequest(): MessagingResponse
    {
        $this->smsCommunicationService->newSmsMessagesReceived(Yii::$app->request->post());

        $response = new MessagingResponse();
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/xml');
        return $response;
    }

    /**
     * @api {post} /v1/twilio/messaging-status-callback Messaging status callback action
     * @apiVersion 0.1.0
     * @apiName MessagingStatusCallback
     * @apiGroup Twilio
     *
     * @apiParam {string}           [SmsSid]   SMS ID
     * @apiParam {string}           [SmsSid]   SMS status
     *
     * @apiSuccess {String} message    Response Id
     * @apiSuccess {String} id    SMS ID
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * {
            "message": "ok",
            "id": "1a3d5",
        }
     *
     */
    public function actionMessagingStatusCallback(): array
    {
        $sms_sid = Yii::$app->request->post('SmsSid');
        $sms_status = Yii::$app->request->post('SmsStatus');

        $sms = Sms::find()->where(['s_tw_message_sid' => $sms_sid])->one();

        if ($sms) {
            if ($sms_status == TwilioClient::STATUS_DELIVERED) {
                $sms->s_status_id = Sms::STATUS_DONE;
                $sms->s_status_done_dt = date('Y-m-d H:i:s');
                $this->smsCommunicationService->addSmsFinishJob($sms);
                $sms->save();
                $this->smsCommunicationService->updateSmsStatus($sms);
            } elseif ($sms_status == TwilioClient::STATUS_FAILED) {
                $sms->s_status_id = Sms::STATUS_ERROR;
                $sms->s_error_message = 'STATUS_FAILED';
                $sms->save();
                $this->smsCommunicationService->updateSmsStatus($sms);
            } elseif ($sms_status == TwilioClient::STATUS_UNDELIVERED) {
                $sms->s_status_id = Sms::STATUS_ERROR;
                $sms->s_error_message = 'STATUS_UNDELIVERED';
//              $sms->sq_tw_status = $sms_status;
                $sms->save();
                $this->smsCommunicationService->updateSmsStatus($sms);
            } elseif ($sms_status == TwilioClient::STATUS_SENT) {
                $sms->s_status_id = Sms::STATUS_SENT;
                $sms->s_error_message = 'STATUS_SENT';
//              $sms->sq_tw_status = $sms_status;
                $sms->save();
                $this->smsCommunicationService->updateSmsStatus($sms);
            } else {
//              $sms->s_status_id = Sms::STATUS_SENT;
                $sms->s_error_message = $sms_status;
//              $sms->s_tw_status = $sms_status;
                $sms->save();
                $this->smsCommunicationService->updateSmsStatus($sms);
            }
        } else {
            if ($sms_status !== TwilioClient::STATUS_SENT) {
                Yii::error('Not found SMS message_id: ' . $sms_sid, 'API:Twilio:MessagingStatusCallback:SmsQueue:find');
                throw new \RuntimeException('Not found SMS message_id: ' . $sms_sid . ' : SmsStatus:' . $sms_status);
            }
        }

        return ['message' => 'ok', 'id' => $sms_sid];
    }

    /**
     * @api {post} /v1/twilio/messaging-fallback Messaging status fallback
     * @apiVersion 0.1.0
     * @apiName MessagingFallback
     * @apiGroup Twilio
     *
     * @apiParam {string}           SmsSid   SMS ID
     *
     * @apiSuccess {String}     message    Response Id
     * @apiSuccess {String}     id    SMS ID
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * {
            "message": "ok",
            "id": "1a3d5",
        }
     *
     */
    public function actionMessagingFallback()
    {
        // Yii::info('actionMessagingFallback ' . VarDumper::dumpAsString(Yii::$app->request->post(), 10), 'info\API:Twilio:MessagingFallback:post');

        $sms_sid = Yii::$app->request->post('SmsSid');
        $sms = Sms::find()->where(['s_tw_message_sid' => $sms_sid])->one();

        if ($sms) {
            $sms->s_status_id = Sms::STATUS_ERROR;
            $sms->s_error_message = 'STATUS_FAILED';
            $sms->save();
            $this->smsCommunicationService->updateSmsStatus($sms);
        } else {
            Yii::error('Not found SMS message_id: ' . $sms_sid, 'API:Twilio:MessagingFallback:SmsQueue:find');
            throw new \RuntimeException('Not found SMS message_id: ' . $sms_sid);
        }

        Yii::error(VarDumper::dumpAsString(Yii::$app->request->post()), 'API:Twilio:MessagingFallback');

        return ['message' => 'ok', 'id' => $sms_sid];
    }

    /**
     * @api {get, post} /v1/twilio/request Request
     * @apiVersion 0.1.0
     * @apiName Request
     * @apiGroup Twilio
     *
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * XML
     *
     */
    public function actionRequest()
    {

        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) === 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        $out = [
            'message'   => 'Server Name: ' . Yii::$app->request->serverName,
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
            <Number>' . Yii::$app->request->get('phone') . '</Number>
        </Dial>
</Response>';

        /*      $xml = '<Response><Dial timeout="10" record="true">
                    <Sip>'. $tosip .'</Sip>
                </Dial>
        </Response>';*/
        echo  $xml;
        exit;
    }

    /**
     * @api {get, post} /v1/twilio/fallback Fallback
     * @apiVersion 0.1.0
     * @apiName fallback
     * @apiGroup Twilio
     *
     *
     */
    public function actionFallback()
    {


        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        $out = [
            'message'   => 'Server Name: ' . Yii::$app->request->serverName,
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
    /**
     * @api {post} /v1/twilio/redirect-call Call redirect action
     * @apiVersion 0.1.0
     * @apiName RedirectCall
     * @apiGroup Twilio
     *
     * @apiParam {Integer}          id                              User ID
     * @apiParam {String}           type                            Type
     * @apiParam {Array}            CallData                        Call data array
     * @apiParam {String}               CallData.CallSid            Call id
     *
     * * @apiParamExample {json} Request-Example:
     * {
     *      "id": "1",
     *      "type": "department",
     *      "isTransfer": "1",
     *      "sid": "CA5f9021ea6f3866sad8e4dasdfcbcc2",
     *      "CallData": {
     *          "AccountSid": "ACccda29b38659fc9d75a2a0b",
     *          "ApiVersion": "2000-04-01",
     *          "CallSid": "CA5f9021ea6f3866b6f4678e4fdafcbcc2",
     *          "CallStatus": "in-progress",
     *          "Called": "+123456789",
     *          "CalledCity": "",
     *          "CalledCountry": "US",
     *          "CalledState": "",
     *          "CalledZip": "",
     *          "Caller": "+123456789",
     *          "CallerCity": "San Francisco",
     *          "CallerCountry": "US",
     *          "CallerState": "CA",
     *          "CallerZip": "",
     *          "Direction": "inbound",
     *          "From": "+123456789",
     *          "FromCity": "San Francisco",
     *          "FromCountry": "US",
     *          "FromState": "CA",
     *          "FromZip": "",
     *          "To": "+123456789",
     *          "ToCity": "",
     *          "ToCountry": "US",
     *          "ToState": "",
     *          "ToZip": ""
     *      }
     * }
     * @apiSuccess {String} responseTwml    Xml Response
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * {
     *      "responseTwml": "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Response><Say language=\"en-US\" voice=\"alice\"></Say></Response>\n"
     * }
     **@apiErrorExample Error-Response:
     *     HTTP/1.1 200
     * {
     *      "responseTwml": "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Response><Say language=\"en-US\" voice=\"alice\">Sorry, communication error</Say></Response>\n"
     * }
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

            $isTransfer = (bool)Yii::$app->request->post('isTransfer', false);

            $call = Call::find()->where(['c_call_sid' => $sid])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();

            if ($call->isOut()) {
                $call->c_from = $call->c_to;
            }

//            if ($isTransfer && $call->isOut()) {
//                $from = $call->c_from;
//                $call->c_from = $call->c_to;
//                $call->c_to = $from;
//            }
            if ($isTransfer) {
                if ($type === 'user') {
                    $call->c_source_type_id = Call::SOURCE_DIRECT_CALL;
                } elseif ($type === 'department') {
                    $call->c_source_type_id = Call::SOURCE_GENERAL_LINE;
                } else {
                    $call->c_source_type_id = Call::SOURCE_TRANSFER_CALL;
                }
                $call->setTypeIn();
            }

            // Yii::info(VarDumper::dumpAsString($callData), 'info\API:Twilio:RedirectCall:callData');

            $responseTwml = new VoiceResponse();

            /** @var Call $call */
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
                        $depId = (int)Yii::$app->request->post('dep_id');
                        if ($depId) {
                            $call->c_dep_id = $depId;
                        }
                        $call->c_created_user_id = $id;
                        $call->resetDataRepeat();
                        $call->resetDataQueueLongTime();
                        $call->setDataPriority(Call::DEFAULT_PRIORITY_VALUE);
                        $call->c_to = null;
                        if (!$call->save()) {
                            Yii::error(VarDumper::dumpAsString($call->errors), 'API:Twilio:RedirectCall:Call:update:1');
                        }
                        Call::applyCallToAgentAccess($call, $id);
                    } else {
                        if (!$call->save()) {
                            Yii::error(VarDumper::dumpAsString($call->errors), 'API:Twilio:RedirectCall:Call:update:2');
                        }
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
                        $call->c_to = $depPhone->dpp_phone_list_id ? $depPhone->phoneList->pl_phone_number : null;
                        $call->setDataPriority($depPhone->dpp_priority);
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
                                $responseTwml->say(
                                    'Your call has been forwarded to the ' . strtolower($depPhone->dppDep->dep_name) . ' department. Please wait for an answer',
                                    [
                                        'language' => 'en-US',
                                        'voice' => 'alice'
                                    ]
                                );
                            }

                            if (isset($ivrParams['hold_play']) && $ivrParams['hold_play']) {
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

                        $delayJob = 7;
                        $job = new CallQueueJob();
                        $job->call_id = $call->c_id;
                        $job->delay = 0;
                        $job->delayJob = $delayJob;
                        $jobId = Yii::$app->queue_job->delay($delayJob)->priority(80)->push($job);

                        try {
                            if (!$jobId) {
                                throw new \DomainException('Not created CallQueueJob');
                            }
                            $dParams = @json_decode($depPhone->dpp_params, true);
                            $repeatParams = $dParams['queue_repeat'] ?? [];
                            if ($repeatParams) {
                                (new RepeatMessageCallJobCreator())->create($call, $depPhone->dpp_id, $repeatParams);
                            }
                            $queueLongTimeParams = new QueueLongTimeNotificationParams(empty($dParams['queue_long_time_notification']) ? [] : $dParams['queue_long_time_notification']);
                            if ($queueLongTimeParams->isActive()) {
                                (new QueueLongTimeNotificationJobCreator())->create($call, $depPhone->dpp_id, $queueLongTimeParams->getDelay());
                            }
                        } catch (\Throwable $e) {
                            Yii::error([
                                'message' => 'Create repeat call job Error.',
                                'useCase' => 'Transfer to department use case.',
                                'error' => $e->getMessage(),
                                'call' => $call->getAttributes(),
                            ], 'CallQueueRepeatMessageJob::create');
                        }
                    } else {
                        throw new Exception('Not found DepartmentPhoneProject', 10);
                    }

                    if (!$call->save()) {
                        Yii::error(VarDumper::dumpAsString($call->errors), 'API:Twilio:RedirectCall:Call:update:3');
                    }
                }

//                if (!$call->save()) {
//                    Yii::error(VarDumper::dumpAsString($call->errors), 'API:Twilio:RedirectCall:Call:update');
//                }
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
            Yii::error(AppHelper::throwableFormatter($throwable), 'API:Twilio:RedirectCall:Throwable');

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

    /**
     * @api {get, post} /v1/twilio/redirect-call-middleware Redirect Call Middleware
     * @apiVersion 0.1.0
     * @apiName RedirectCallMiddleware
     * @apiGroup Twilio
     *
     *
     **@apiErrorExample Error-Response:
     *     HTTP/1.1 200
     *
     *     <?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Response><Say language=\"en-US\" voice=\"alice\">Sorry, connection failed. Action Redirect Call</Say></Response>\n
     * @return mixed
     */
    public function actionRedirectCallMiddleware(): string
    {
        Yii::info(VarDumper::dumpAsString(['post' => Yii::$app->request->post(), 'get' => Yii::$app->request->get()]), 'info\API:TwilioController:actionRedirectCall');

        $paramsStr = Yii::$app->request->get('params');
        $post = Yii::$app->request->post();

        if ($paramsStr) {
            $params = json_decode($paramsStr, true);
        } else {
            $params = [];
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/xml');

        $voiceResponse = new VoiceResponse();

        if ($params) {
            $callbackUrl = $params['callBackUrl'] ?? '';
            $requestData = $params['data'] ?? [];

            $requestData['CallData'] = $post;


            $responseData = [
                'message' => 'ok',
                'params' => $params,
                'post' => $post,
                'get' => Yii::$app->request->get()
            ];

            try {
                $client = new Client();
                $client->setTransport(CurlTransport::class);
                $request = $client->createRequest();
                $request->setMethod('POST')
                    ->setUrl($callbackUrl)
                    ->setData($requestData);
                $response = $request->send();

                if ($response->isOk) {
                    $responseData['responseData'] = $response->data;

                    if (isset($responseData['responseData']['responseTwml']) && $responseData['responseData']['responseTwml']) {
                        return (string) $responseData['responseData']['responseTwml'];
                    }

                    $voiceResponse->say('Redirect Call. Invalid server response');
                } else {
                    $responseData['message'] = 'error';
                    $responseData['responseContent'] = $response->content;
                    $voiceResponse->say('Invalid request. Action Redirect Call');
                }
            } catch (\Throwable $throwable) {
                $voiceResponse->say('CURL Error. Action Redirect Call');
                $responseData['error'] = $throwable->getLine() . ' - ' . $throwable->getMessage();
            }

            $voiceResponse->reject(['reason' => 'busy']);
        } else {
            Yii::error('Not found GET "params" ' . VarDumper::dumpAsString($paramsStr), 'API:TwilioController:actionRedirectCall:params');
            $responseData['error'] = 'Not found GET "params"';
            $voiceResponse->say('Sorry, connection failed. Action Redirect Call');
            $voiceResponse->reject(['reason' => 'busy']);
        }
        return (string)$voiceResponse;
    }

    public function actionConferenceStatusCallback(): array
    {
        $apiLog = $this->startApiLog($this->action->uniqueId);

//      $post = Yii::$app->request->post();
//      $responseData = [
//          'post'      => $post
//      ];
//
//      $conferenceSid = mb_substr(Yii::$app->request->post('ConferenceSid'), 0, 34);
//
//      try {
//
//          if ($conferenceSid) {
//
//              $cf = Conference::findOne(['cf_sid' => $conferenceSid]);
//              if (!$cf) {
//                  $cf = new Conference();
//                  $cf->cf_sid = $conferenceSid;
//                  $cf->cf_friendly_name = Yii::$app->request->post('FriendlyName');
//                  $cf->cf_call_sid = Yii::$app->request->post('CallSid');
//
//                  if (!$cf->save()) {
//                      Yii::error(VarDumper::dumpAsString($cf->errors),
//                          'API:TwilioController:actionConferenceStatusCallback:Conference:save');
//                  }
//              }
//
//              $cLog = new ConferenceLog();
//              $cLog->cl_cf_id = $cf->cf_id;
//              $cLog->cl_cf_sid = $conferenceSid;
//              $cLog->cl_sequence_number = Yii::$app->request->post('SequenceNumber');
//              $cLog->cl_status_callback_event = Yii::$app->request->post('StatusCallbackEvent');
//              $cLog->cl_json_data = json_encode($post, JSON_THROW_ON_ERROR);
//
//              if (!$cLog->save()) {
//                  Yii::error(VarDumper::dumpAsString($cf->errors),
//                      'API:TwilioController:actionConferenceStatusCallback:ConferenceLog:save');
//              }
//
//              $data = [
//                  'uniqid' => uniqid('', true),
//                  'conferenceData' => $post,
//                  'conference' => $cf->attributes,
//              ];
//
//              $this->communicationService->voiceConferenceCallback($data);
//
//              $responseData['conference'] = $cf->attributes;
//              $responseData['conference_log'] = $cLog->attributes;
//
//          } else {
//              $responseData['error'] = 'Not found ConferenceSid';
//              Yii::error('Not found POST "ConferenceSid" ' . VarDumper::dumpAsString($post),
//                  'API:TwilioController:actionConferenceStatusCallback');
//          }
//      } catch (\Throwable $throwable) {
//          $responseData['error'] = $throwable->getMessage() . ', ' .  $throwable->getFile() . ':' .  $throwable->getLine();
//
//          Yii::error($responseData['error'],
//              'API:TwilioController:actionConferenceStatusCallback:Throwable');
//      }

        $responseData = [
            'date'      => date('Y-m-d'),
            'ip'        => Yii::$app->request->getUserIP(),
            'get'       => Yii::$app->request->get(),
            'post'      => Yii::$app->request->post(),
        ];

        Yii::warning(VarDumper::dumpAsString($responseData), 'Twilio ConferenceStatusCallback');


        $conferenceSid = Yii::$app->request->post('ConferenceSid');

//        [
//            'Coaching' => 'false'
//    'FriendlyName' => 'room1'
//    'SequenceNumber' => '1'
//    'ConferenceSid' => 'CF4a296dff23784c3e25e4f166529fa0ff'
//    'EndConferenceOnExit' => 'true'
//    'CallSid' => 'CA16e5e86828dbb27fef7bff9b1a40a645'
//    'StatusCallbackEvent' => 'participant-join'
//    'Timestamp' => 'Fri, 25 Oct 2019 06:37:44 +0000'
//    'StartConferenceOnEnter' => 'true'
//    'Hold' => 'false'
//    'AccountSid' => 'AC10f3c74efba7b492cbd7dca86077736c'
//    'Muted' => 'true'
//]

        $apiLog->endApiLog($responseData);

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $responseData;
    }

    /**
     * @api {post} /v1/twilio/conference-recording-status-callback Conference Recording Status Callback
     * @apiVersion 0.1.0
     * @apiName ConferenceRecordingStatusCallback
     * @apiGroup Twilio
     *
     * @apiParam {Integer}          CallSid                         Call id
     * @apiParam {String}           [ConferenceSid]                   Conference id
     * @apiParam {String}           [RecordingSid]                    Recording id
     * @apiParam {String}           [RecordingUrl]                    Recording Url
     * @apiParam {String}           [RecordingDuration]               Recording Duration
     *
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *  <?xml version="1.0" encoding="UTF-8"?>
     *  <Response/>
     */
    public function actionConferenceRecordingStatusCallback()
    {
        $apiLog = $this->startApiLog($this->action->uniqueId);

        $post = Yii::$app->request->post();
        $responseData = [
            'post'      => $post,
        ];

        $callSid = Yii::$app->request->post('CallSid');
        $conferenceSid = Yii::$app->request->post('ConferenceSid');
        $recordingSid = Yii::$app->request->post('RecordingSid');
        $recordingUrl = Yii::$app->request->post('RecordingUrl');
        $recordingDuration = Yii::$app->request->post('RecordingDuration');

        if (!$recordingUrl) {
            Yii::error('Not found RecordingUrl', 'API:TwilioController:actionConferenceRecordingStatusCallback');
            throw new BadRequestHttpException('Not found RecordingUrl');
        }

        if ($conferenceSid) {
            $conference = Conference::find()->where(['cf_sid' => $conferenceSid])->one();
            if ($conference) {
                $conference->cf_recording_url = $recordingUrl;
                $conference->cf_recording_duration = $recordingDuration;
                $conference->cf_recording_sid = $recordingSid;
                $conference->cf_updated_dt = date('Y-m-d H:i:s');
                if (!$conference->save()) {
                    Yii::error(VarDumper::dumpAsString($conference->errors), 'API:TwilioController:actionConferenceRecordingStatusCallback:Conference:update');
                }

                $data = [
                    'uniqid' => uniqid('', true),
                    'conferenceData' => $post,
                    'conference' => $conference->attributes,
                ];

                $this->communicationService->voiceConferenceRecordCallback($data);

                $responseData['conference'] = $conference->attributes;
            }
        }

        $apiLog->endApiLog($responseData);
        $response = new MessagingResponse();
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/xml');
        echo $response;
        exit;
    }

    /**
     * @api {post} /v1/twilio/cancel-call Cancel Call
     * @apiVersion 0.1.0
     * @apiName CancelCall
     * @apiGroup Twilio
     *
     * @apiParam {Integer}          c_id                         Call id
     * @apiParam {String}           [ConferenceSid]                   Conference id
     * @apiParam {String}           [RecordingSid]                    Recording id
     * @apiParam {String}           [RecordingUrl]                    Recording Url
     * @apiParam {String}           [RecordingDuration]               Recording Duration
     *
     *
     * @apiSuccess {String} responseTwml    Xml Response
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * {
     *      "responseTwml": "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Response><Say language=\"en-US\" voice=\"alice\"></Say></Response>\n"
     * }
     **@apiErrorExample Error-Response:
     *     HTTP/1.1 200
     * {
     *      "responseTwml": "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Response><Say language=\"en-US\" voice=\"alice\">Sorry, communication error</Say></Response>\n"
     * }
     *
     * @return mixed
     */
    public function actionCancelCall()
    {

        //$this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);

//        $out = [
////            'dateTime'      => date('Y-m-d H:i:s'),
////            'ip'        => Yii::$app->request->getUserIP(),
//            'get'       => Yii::$app->request->get(),
//            'post'      => Yii::$app->request->post(),
//        ];

        // Yii::info(VarDumper::dumpAsString($out), 'info\API:Twilio:RedirectCal');

        $c_id = (int) Yii::$app->request->post('c_id');

        try {
            if (!$c_id) {
                throw new Exception('Params "c_id" is empty', 1);
            }

            $call = Call::findOne($c_id);

            // Yii::info(VarDumper::dumpAsString($callData), 'info\API:Twilio:CancelCall:callData');

            $responseTwml = new VoiceResponse();

            if ($call) {
                $call->setStatusCanceled();

                $message = Yii::$app->params['settings']['call_incoming_time_limit_message'] ?? '';

                if ($message) {
                    $responseTwml->say(
                        $message,
                        [
                            'language' => 'en-US',
                            'voice' => 'alice'
                        ]
                    );
                }

                $responseTwml->reject(['reason' => 'busy']);

//                $url_music = 'https://talkdeskapp.s3.amazonaws.com/production/audio_messages/folk_hold_music.mp3';
//                $responseTwml->play($url_music, ['loop' => 0]);

                if (!$call->save()) {
                    Yii::error(VarDumper::dumpAsString($call->errors), 'API:Twilio:CancelCall:Call:update');
                }
            }
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable), 'API:Twilio:CancelCall:Throwable');

            $responseTwml = new VoiceResponse();
            $responseTwml->say('Sorry, sale communication error', [
                'language' => 'en-US',
                'voice' => 'alice'
            ]);
        }
        $responseData['responseTwml'] = (string) $responseTwml;

        $apiLog->endApiLog($responseData);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $responseData;
    }

    /**
     * @return mixed
     *@api {post} /v1/twilio/call-request Call Request
     * @apiVersion 0.1.0
     * @apiName CallRequest
     * @apiGroup Twilio
     *
     * @apiParam {String}           [from_number]                   number
     * @apiParam {String}           [number]                        number
     *
     *
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * <?xml version="1.0" encoding="UTF-8"?>
     *      <Response>
     *          <Dial recordingStatusCallbackMethod="POST" callerId="" record="record-from-answer-dual" recordingStatusCallback="https://api.sales/v1/twilio/recording-status-callback">
     *                  <Number statusCallbackEvent="ringing answered completed" statusCallback="https://api.sales/v1/twilio/voice-status-callback" statusCallbackMethod="POST"/>
     *          </Dial>
    * </Response>
     *
     */
    public function actionCallRequest(): VoiceResponse
    {
        $responseData = [];
        $apiLog = $this->startApiLog($this->action->uniqueId);

        /*
         *      'SipCallId' => '7a7501ca9187ce7483670dc8b9a5a4ed@0.0.0.0'
                'ApiVersion' => '2010-04-01'
                'SipResponseCode' => '200'
                'Called' => 'sip:alex.connor@kivork.sip.us1.twilio.com'
                'Caller' => 'BotDialer'
                'CallStatus' => 'in-progress'
                'CallSid' => 'CAe2dc673be7dd4d108db7a4a2bed45be4'
                'To' => 'sip:alex.connor@kivork.sip.us1.twilio.com'
                'From' => 'BotDialer'
                'Direction' => 'outbound-api'
                'AccountSid' => 'AC10f3c74efba7b492cbd7dca86077736c'

         */

        $fromPhoneNumber = Yii::$app->request->get('from_number');
        $phoneNumberToDial = Yii::$app->request->get('number');

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/xml');

        $response =  new VoiceResponse();

        $dial = $response->dial('', [
            'recordingStatusCallbackMethod' => 'POST' ,
            'callerId' => $fromPhoneNumber,
            'record' => 'record-from-answer-dual',
            'recordingStatusCallback' => $this->recordingStatusCallbackUrl
        ]);

        $dial->number($phoneNumberToDial, [
            'statusCallbackEvent' => 'ringing answered completed',
            'statusCallback' => $this->voiceStatusCallbackUrl,
            'statusCallbackMethod' => 'POST',
        ]);
        $apiLog->endApiLog($responseData);
        return  $response;
    }

    /**
     * @return mixed
     *@api {post} /v1/twilio/voice-request Voice Request
     * @apiVersion 0.1.0
     * @apiName VoiceRequest
     * @apiGroup Twilio
     *
     * @apiParam {DTO}           RequestDataDTO                   RequestData
     * @apiParam {String}           RequestDataDTO.To               Request to
     *
     *
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * <?xml version="1.0" encoding="UTF-8"?>
     *      <Response>
     *         <Reject reason="busy"/>
     *      </Response>
     *
     */
    public function actionVoiceRequest()
    {
        $responseData = [];
        $apiLog = $this->startApiLog($this->action->uniqueId);
        $requestData = new RequestDataDTO(Yii::$app->request->post());
        $response = new VoiceResponse();

        try {
            if (empty($requestData->To)) {
                $responseData['error_code'] = 27;
                throw new \RuntimeException('Not isset requestData[To]', 27);
            }

            if (empty($requestData->From) && empty($requestData->FromAgentPhone) && empty($requestData->projectId)) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
                Yii::$app->response->headers->add('Content-Type', 'text/xml');
                return $this->communicationService->callFromJwtClient($requestData, $apiLog);
            }

            $phone_number = PhoneList::findOne(['pl_phone_number' => $requestData->To]);
            if (!$phone_number) {
                $responseData['error_code'] = 25;
                throw new \RuntimeException('Phone number not found. ' . $requestData->To, 25);
            }

            $result = $this->communicationService->voiceIncoming($requestData);
            $responseData = $this->communicationService->getResponseData($result, $apiLog);

            $responseArr = $responseData['data']['response'];
            if (isset($responseArr['data'], $responseArr['data']['response'])) {
                $responseArr = $responseArr['data']['response'];
            }

            if ($responseArr) {
                $general_phone_number = $responseArr['general_phone_number'] ?? null;
                $agent_username = $responseArr['agent_username'] ?? [];
                $call_to_hold = $responseArr['call_to_hold'] ?? 0;
                $call_to_general = $responseArr['call_to_general'] ?? 0;
                $twml = (isset($responseArr['twml']) && $responseArr['twml'] ) ? $responseArr['twml'] : false ;

                if ($twml) {
                    $apiLog->endApiLog($responseData);
                    Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
                    Yii::$app->response->headers->add('Content-Type', 'text/xml');
                    return $twml;
                }

                if ($call_to_hold > 0) {
                    if (!$twml) {
                        $response->say('We apologize, but all of our agents are currently assisting other customers. Please hold for the next available agent.', [
                            'language' => 'en-US',
                            'voice' => 'alice',
                        ]);
                        $response->play('https://talkdeskapp.s3.amazonaws.com/production/audio_messages/folk_hold_music.mp3');
                    } else {
                        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
                        Yii::$app->response->headers->add('Content-Type', 'text/xml');
                        return $twml;
                    }
                } elseif ($call_to_general > 0) {
                    $dial = $response->dial('', [
                        'recordingStatusCallbackMethod' => 'POST',
                        'callerId' => $requestData->From,
                        'record' => 'record-from-answer-dual',
                        'recordingStatusCallback' => $this->recordingStatusCallbackUrl
                    ]);
                    $dial->number($general_phone_number, [
                        'statusCallbackEvent' => 'ringing answered completed',
                        'statusCallback' => $this->voiceStatusCallbackUrl,
                        'statusCallbackMethod' => 'POST',
                    ]);
                } elseif ($agent_username && is_array($agent_username) && count($agent_username)) {
                    $dial = $response->dial('', [
                        'recordingStatusCallbackMethod' => 'POST',
                        'callerId' => $requestData->From,
                        'record' => 'record-from-answer-dual',
                        'recordingStatusCallback' => $this->recordingStatusCallbackUrl
                    ]);
                    $usersNames = [];
                    foreach ($agent_username as $username) {
                        $dial->client($username, [
                            'statusCallbackEvent' => 'ringing answered completed',
                            'statusCallback' => $this->voiceStatusCallbackUrl,
                            'statusCallbackMethod' => 'POST',
                        ]);
                        $usersNames[] = $username;
                    }
                } else {
                    $responseData['error_code'] = 22;
                    throw new \RuntimeException('Error:  Not found clients or general_phone_number for call', 22);
                }
            } else {
                $responseData['error_code'] = 21;
                throw new \RuntimeException('Error:  Not found dataResponseArr[data][response]', 21);
            }
        } catch (\RuntimeException $e) {
            $response =  new VoiceResponse();
            $response->reject(['reason' => 'busy']);

            $responseData['error'] = $e->getMessage();
            if (!isset($responseData['error_code']) || !$responseData['error_code']) {
                $responseData['error_code'] = 20;
            }
            $responseData['message'] =  $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
            \Yii::error($responseData['message'], 'API:Twilio:VoiceRequest:Throwable');
        }

        if (empty($responseData['error'])) {
            $responseData['xml'] = (string)$response;
        }
        $apiLog->endApiLog($responseData);
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/xml');
        return $response;
    }

    /**
     * @return mixed
     *@api {post} /v1/twilio/voice-request Voice Request
     * @apiVersion 0.1.0
     * @apiName VoiceRequest
     * @apiGroup Twilio
     *
     * @apiParam {DTO}           RequestDataDTO                   RequestData
     * @apiParam {String}           RequestDataDTO.To               Request to
     *
     *
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * <?xml version="1.0" encoding="UTF-8"?>
     *      <Response>
     *         <Reject reason="busy"/>
     *      </Response>
     *
     */
    public function actionVoiceGather()
    {
        $responseData = [];
        $apiLog = $this->startApiLog($this->action->uniqueId);
        $requestData = new RequestDataDTO(Yii::$app->request->post());
        $getData = \Yii::$app->request->get();
        $response = new VoiceResponse();

        try {
            if (empty($requestData->To)) {
                $responseData['error_code'] = 27;
                throw new \RuntimeException('Not isset requestData[To]', 27);
            }

            $phone_number = PhoneList::findOne(['pl_phone_number' => $requestData->To]);
            if (!$phone_number) {
                $responseData['error_code'] = 25;
                throw new \RuntimeException('Phone number not found. ' . $requestData->To, 25);
            }

            if (empty($requestData->CallSid)) {
                throw new \RuntimeException('actionVoiceGather not found CallSid: ' . $phone_number->pl_phone_number .
                    "\n" . VarDumper::dumpAsString($requestData, 10, false));
            }

            $result = $this->communicationService->voiceIncoming($requestData);
            $dataResponseArr = $this->communicationService->getResponseData($result, $apiLog);

            $responseArr = $dataResponseArr['data']['response'];
            if (!$dataResponseArr) {
                throw new \RuntimeException("Error: Sales response: \n" .  VarDumper::dumpAsString($dataResponseArr));
            }
            if (isset($responseArr['data'], $responseArr['data']['response'])) {
                $responseArr = $responseArr['data']['response'];
            }

            $twml = (isset($responseArr['twml']) && $responseArr['twml'] ) ? $responseArr['twml'] : false ;

            if ($twml) {
                $apiLog->endApiLog($dataResponseArr);
                Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
                Yii::$app->response->headers->add('Content-Type', 'text/xml');
                return $twml;
            }

            throw new \RuntimeException("Error: Sales response TWML not found: \n" .  VarDumper::dumpAsString($dataResponseArr));
        } catch (\RuntimeException $e) {
            $response =  new VoiceResponse();
            $response->reject(['reason' => 'busy']);

            $responseData['error'] = $e->getMessage();
            if (!isset($responseData['error_code']) || !$responseData['error_code']) {
                $responseData['error_code'] = 20;
            }
            $responseData['message'] =  $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
            \Yii::error($responseData['message'], 'API:Twilio:actionVoiceGather:Throwable');
        }

        $apiLog->endApiLog($responseData);
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/xml');
        return $response;
    }

    /**
     *@api {post} /v1/twilio/recording-status-callback Recording Status Callback
     * @apiVersion 0.1.0
     * @apiName RecordingStatusCallback
     * @apiGroup Twilio
     *
     * @apiParam {String}           CallSid                   Call ID
     * @apiParam {String}           RecordingSid              Recording ID
     *
     *
     *
     * @apiErrorExample Error-Response:
     *      HTTP/1.1 400 Bad Request
     * {
     *      "name": "Bad Request",
     *      "message": "Not found Call by SID",
     *      "code": 0,
     *      "status": 400,
     *      "type": "yii\\web\\BadRequestHttpException"
     * }
     * @return mixed
     * @return MessagingResponse|null
     * @throws BadRequestHttpException
     */
    public function actionRecordingStatusCallback(): ?MessagingResponse
    {
        $apiLog = $this->startApiLog($this->action->uniqueId);

        $callSid = Yii::$app->request->post('CallSid');
        $recordingSid = Yii::$app->request->post('RecordingSid');

        if (!$callSid) {
            Yii::error('Not found CallSid', 'API:TwilioController:actionRecordingStatusCallback');
            throw new BadRequestHttpException('Not found CallSid');
        }

        if (!$recordingSid) {
            Yii::error('Not found RecordingSid', 'API:TwilioController:actionRecordingStatusCallback');
            throw new BadRequestHttpException('Not found RecordingSid');
        }

        if ($callSid && $recordingSid) {
            $call = Call::find()->where(['c_call_sid' => $callSid])->one();
            if ($call) {
                $call->c_recording_duration = Yii::$app->request->post('RecordingDuration');
                $call->c_recording_sid = Yii::$app->request->post('RecordingSid');
                $call->c_updated_dt = date('Y-m-d H:i:s');
                if (!$call->save()) {
                    Yii::error(VarDumper::dumpAsString($call->errors, 10), 'API:TwilioController:actionRecordingStatusCallback:Call:save');
                }

                $callData = Yii::$app->request->post();

                $callData['c_project_id'] = $call->c_project_id;

                $data = [
                    'uid' => uniqid('', true),
                    'c_id' => $call->c_id,
                    'api_user_id' => null,
                    'c_call_status' => $call->c_call_status,
                    'c_project_id' => $call->c_project_id,
                    'c_tw_price' => $call->c_price,
                    'c_endpoint' => null,
                    'callData' => $callData,
                    'call' => $call->attributes,
                ];

                $data['call']['c_project_id'] = $call->c_project_id;
                $this->communicationService->voiceRecord($data);

                $responseData['call'] = $call->attributes;
                $apiLog->endApiLog($responseData);
                $response = new MessagingResponse();
                Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
                Yii::$app->response->headers->add('Content-Type', 'text/xml');
                return $response;
            }

            Yii::error('Not found Call, sid:' . $callSid, 'API:TwilioController:actionRecordingStatusCallback:Call');
            throw new BadRequestHttpException('Not found Call by SID');
        }

        Yii::error('Not found Call, sid:' . $callSid, 'API:TwilioController:actionRecordingStatusCallback:Call');
        throw new BadRequestHttpException('Not found Call or RecordingSID');
    }

    public function actionVoiceStatusCallback(): ?array
    {
        $apiLog = $this->startApiLog($this->action->uniqueId);
        $job_id = '';
        /*
         * ApiVersion   "2010-04-01"
            Called  "sip:russell@kivork.sip.us1.twilio.com"
            CallStatus  "completed"
            Duration    "1"
            From    "BotDialer"
            Direction   "outbound-api"
            Timestamp   "Tue, 22 Jan 2019 15:34:22 +0000"
            CallDuration    "28"
            AccountSid  "AC10f3c74efba7b492cbd7dca86077736c"
            CallbackSource  "call-progress-events"
            SipCallId   "e91295195415ce2054828213eaa56055@0.0.0.0"
            SipResponseCode "200"
            Caller  "BotDialer"
            SequenceNumber  "3"
            To  "sip:russell@kivork.sip.us1.twilio.com"
            CallSid "CA896e2d274c3a1bc8158d7a79859409f4"
         */



        //        [
//            'ApiVersion' => '2010-04-01'
//            'Called' => 'sip:alex.connor@kivork.sip.us1.twilio.com'
//            'CallStatus' => 'busy'
//            'Duration' => '0'
//            'From' => 'admin'
//            'CallerCountry' => 'CF'
//            'Direction' => 'outbound-api'
//            'Timestamp' => 'Wed, 23 Jan 2019 16:10:45 +0000'
//            'CallDuration' => '0'
//            'CallbackSource' => 'call-progress-events'
//            'AccountSid' => 'AC10f3c74efba7b492cbd7dca86077736c'
//            'SipCallId' => 'f1823a838a932bc286add0eade583e88@0.0.0.0'
//            'CallerCity' => ''
//            'SipResponseCode' => '486'
//            'CallerState' => ''
//            'Caller' => 'admin'
//            'FromCountry' => 'CF'
//            'FromCity' => ''
//            'SequenceNumber' => '2'
//            'CallSid' => 'CA09852647a9fccfffbc98481bd933edcd'
//            'To' => 'sip:alex.connor@kivork.sip.us1.twilio.com'
//            'FromZip' => ''
//            'CallerZip' => ''
//            'FromState' => ''
//        ]

        $vl = new \modules\twilio\src\entities\voiceLog\VoiceLog();

        $vl->vl_call_status = Yii::$app->request->post('CallStatus');
        $vl->vl_call_sid = Yii::$app->request->post('CallSid');
        $vl->vl_parent_call_sid = Yii::$app->request->post('ParentCallSid');
        $vl->vl_account_sid = Yii::$app->request->post('AccountSid');
        $vl->vl_from = Yii::$app->request->post('From');
        $vl->vl_to = Yii::$app->request->post('To');
        $vl->vl_api_version = Yii::$app->request->post('ApiVersion');
        $vl->vl_direction = Yii::$app->request->post('Direction');
        $vl->vl_forwarded_from = Yii::$app->request->post('ForwardedFrom');
        $vl->vl_caller_name = Yii::$app->request->post('CallerName');

        $vl->vl_call_duration = Yii::$app->request->post('CallDuration'); //The duration in seconds of the just-completed call. Only present in the completed event.
        $vl->vl_sip_response_code = Yii::$app->request->post('SipResponseCode');  // Only present in the completed event if the CallStatus is failed or no-answer.
        $vl->vl_recording_url = Yii::$app->request->post('RecordingUrl');    // RecordingUrl is only present in the completed event.
        $vl->vl_recording_sid = Yii::$app->request->post('RecordingSid');     // RecordingSid is only present with the completed event.
        $vl->vl_recording_duration = Yii::$app->request->post('RecordingDuration');    // RecordingDuration is only present in the completed event.
        $vl->vl_timestamp = Yii::$app->request->post('Timestamp');
        $vl->vl_callback_source = Yii::$app->request->post('CallbackSource');
        $vl->vl_sequence_number = Yii::$app->request->post('SequenceNumber');
        $vl->vl_created_dt = date('Y-m-d H:i:s');

        $call = Call::find()->where(['c_call_sid' => $vl->vl_call_sid])->one();

        $parentCall = null;
        if ($vl->vl_parent_call_sid) {
            $parentCall = Call::find()->where(['c_call_sid' => $vl->vl_parent_call_sid])->limit(1)->one();
        }

        if (!$call) {
            $call = new Call();

            $call->c_call_sid = $vl->vl_call_sid;
            $call->c_parent_call_sid = $vl->vl_parent_call_sid;
            $call->c_to = $vl->vl_to;
            $call->c_from = $vl->vl_from;
            $call->c_caller_name = $vl->vl_caller_name; //$requestData['Caller'] ?? null;
            $call->c_created_dt = $vl->vl_created_dt;

            if ($vl->vl_forwarded_from) {
                $call->c_forwarded_from = $vl->vl_forwarded_from;
            }
        }

        if ($call) {
            if ($parentCall) {
                if ($parentCall->c_project_id) {
                    $call->c_project_id = $parentCall->c_project_id;
                }
                $call->c_call_type_id = $parentCall->c_call_type_id;
            } else {
                if (VoipDevice::isValid($call->c_from)) {
                    $call->setTypeOut();
                } else {
                    $call->setTypeIn();
                }
            }

            Yii::info(($parentCall ? 'parent: yes' : 'parent: no') . ', sid: ' . $call->c_call_sid . ', parent: ' . $call->c_parent_call_sid . ', direction-type-id: ' . $call->c_call_type_id . "\r\n" /*. VarDumper::dumpAsString(Yii::$app->request->post())*/, 'info\API:TwilioController:VoiceStatusCallback');

            $call->c_updated_dt = date('Y-m-d H:i:s');
            $call->c_call_status = $vl->vl_call_status;
            $call->c_parent_call_sid = $vl->vl_parent_call_sid;

            if (!$call->save()) {
                Yii::error('Not saved Call: ' . VarDumper::dumpAsString($call->errors), 'API:TwilioController:VoiceStatusCallback:Call:save');
            }

            $postData = Yii::$app->request->post();
            $postData['com_call_id'] =  $call->c_id;

            $this->communicationService->voiceDefault($postData);
        } else {
            Yii::error('Not found c_call_sid: ' . $vl->vl_call_sid, 'API:TwiCall');
        }


        if ($vl->save()) {
            $responseData = ['message' => 'ok', 'voice_log_id' => $vl->vl_id, 'job_id' => $job_id];

            if ($call) {
                $responseData['call']['CallSid'] = $call->c_call_sid;
                $responseData['call']['ParentCallSid'] = $call->c_parent_call_sid;
                $responseData['call']['CallStatus'] = $call->c_call_status;
                $responseData['call']['DirectionTypeId'] = $call->c_call_type_id;
            }

            $responseData = $apiLog->endApiLog($responseData);

            return $responseData;
        } else {
            Yii::error('Not save Voice Log: ' . VarDumper::dumpAsString($vl->errors), 'API:TwilioController:VoiceStatusCallback:VoiceLog:save');
            throw new BadRequestHttpException(VarDumper::dumpAsString($vl->errors));
        }
    }

    /**
     * @api {post} /v1/twilio/redirect-to Redirect To
     * @apiVersion 0.1.0
     * @apiName RedirectTo
     * @apiGroup Twilio
     *
     * @apiParam {String}       from        From
     * @apiParam {String}       to          To
     * @apiParam {String}       type        Type
     *
     *
     * @apiErrorExample Error-Response:
     *      HTTP/1.1 200 OK
     * <?xml version="1.0" encoding="UTF-8"?>
     * <Response>
     *      <Say>Sorry, application error</Say>
     *      <Reject reason="busy"/>
     * </Response>
     */
    public function actionRedirectTo(): string
    {
        $get = Yii::$app->request->get();

        $result = '';

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/xml');

        $responseVoiceResponse = new VoiceResponse();

        try {
            $from = Yii::$app->request->get('from');
            $to = Yii::$app->request->get('to');
            $type = Yii::$app->request->get('type');

            if (!$to) {
                throw new \Exception('Error request params (to)', 5);
            }

            if (!$from) {
                throw new \Exception('Error request params (from)', 6);
            }

            if (!$type) {
                throw new \Exception('Error request params (type)', 7);
            }

            if ($type === 'hold') {
                $responseVoiceResponse->say('Please wait a moment for the agent to be able to answer');
                $responseVoiceResponse->play('http://com.twilio.sounds.music.s3.amazonaws.com/MARKOVICHAMP-Borghestral.mp3');
            } else {
                $dial = $responseVoiceResponse->dial('', [
                    'recordingStatusCallbackMethod' => 'POST',
                    'callerId' => $get['from'],
                    'record' => 'record-from-answer-dual',
                    'recordingStatusCallback' => $this->recordingStatusCallbackUrl
                ]);

                $params = [
                    'statusCallbackEvent' => 'ringing answered completed',
                    'statusCallback' => $this->voiceStatusCallbackUrl,
                    'statusCallbackMethod' => 'POST',
                ];

                if ($type === 'client') {
                    $dial->client($to, $params);
                } elseif ($type === 'sip') {
                    $dial->sip('sip:' . $to);
                } elseif ($type === 'number') {
                    $dial->number($to, $params);
                } else {
                    if (preg_match("/^[\+0-9\-\(\)\s]*$/", $to)) {
                        $dial->number($to, $params);
                    } else {
                        throw new \Exception('Error preg_match number - "to"', 8);
                    }
                }
            }
        } catch (\Throwable $e) {
            $response = [
                'code' => 500,
                'status' => 'error',
                'message' => 'RedirectTo error: ' . $e->getMessage(),
                'data' => [
                    'get' => $get,
                    'result' => $result,
                ],
            ];

            \Yii::error(VarDumper::dumpAsString($response), 'API:TwilioJwtController:actionRedirectTo:Throwable');
            $responseVoiceResponse->say('Sorry, application error');
            $responseVoiceResponse->reject(array('reason' => 'busy'));
        }
        return (string) $responseVoiceResponse;
    }

    /**
     * @api {post} /v1/twilio/check-out-number Check Out Number
     * @apiVersion 0.1.0
     * @apiName CheckOutNumber
     * @apiGroup Twilio
     *
     * @apiParam {String}       number        Number
     ** @apiParamExample {json} Request-Example:
     * {
     *      "available": false
     * }
     *
     * @apiErrorExample Error-Response:
     *      HTTP/1.1 200 OK
     * {
     *      "error": "Not found number"
     * }
     */
    public function actionCheckOutNumber(): array
    {
        $number = (string)Yii::$app->request->post('number');

        if (!$number) {
            return ['error' => 'Not found number'];
        }

        if (ClientPhone::find()->andWhere(['phone' => $number])->exists()) {
            return ['available' => true];
        }

        return ['available' => false];
    }
}
