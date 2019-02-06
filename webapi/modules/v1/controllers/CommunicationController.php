<?php
namespace webapi\modules\v1\controllers;

use common\components\CommunicationService;
use common\models\Call;
use common\models\ClientPhone;
use common\models\Email;
use common\models\Employee;
use common\models\Lead;
use common\models\Notifications;
use common\models\Project;
use common\models\Sms;
use common\models\UserCallStatus;
use common\models\UserConnection;
use common\models\UserGroupAssign;
use common\models\UserProjectParams;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;
use common\components\ReceiveEmailsJob;
use yii\queue\Queue;

class CommunicationController extends ApiBaseController
{

    public const ACTION_GET     = 'get';
    public const ACTION_sET     = 'set';
    public const ACTION_READ    = 'read';
    public const ACTION_UPDATE  = 'update';
    public const ACTION_CREATE  = 'create';
    public const ACTION_DELETE  = 'delete';

    public const TYPE_VOIP_RECORD       = 'voip_record';
    public const TYPE_VOIP_INCOMING     = 'voip_incoming';
    public const TYPE_VOIP              = 'voip';

    public const TYPE_UPDATE_EMAIL_STATUS = 'update_email_status';
    public const TYPE_UPDATE_SMS_STATUS = 'update_sms_status';

    public const TYPE_NEW_EMAIL_MESSAGES_RECEIVED = 'new_email_messages_received';
    public const TYPE_NEW_SMS_MESSAGES_RECEIVED = 'new_sms_messages_received';

    /**
     * @api {post} /v1/communication/email Communication Email
     * @apiVersion 0.1.0
     * @apiName CommunicationEmail
     * @apiGroup Communication
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization    Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *      "type": "update_email_status",
     *      "eq_id": 127,
     *      "eq_status_id": 5,
     *  }
     *
     *
     * @return array
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     * @throws \yii\web\BadRequestHttpException
     */

    public function actionEmail(): array
    {
        $this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);

        //$action = Yii::$app->request->post('action');
        $type = Yii::$app->request->post('type');
        $last_id = Yii::$app->request->post('last_email_id', NULL);
        /*if(!$action) {
            throw new NotFoundHttpException('Not found action', 1);
        }*/

        if(!$type) {
            throw new NotFoundHttpException('Not found Email type', 1);
        }


        switch ($type) {
            case self::TYPE_UPDATE_EMAIL_STATUS : $response = $this->updateEmailStatus();
                break;
            case self::TYPE_NEW_EMAIL_MESSAGES_RECEIVED : $response = $this->newEmailMessagesReceived($last_id);
                break;
            default: throw new BadRequestHttpException('Invalid Email type', 2);
        }

        $responseData = [];

        if (isset($response['error']) && $response['error']) {

        } else {
            $responseData = [
                'status'    => 200,
                'name'      => 'Success',
                'code'      => 0,
                'message'   => ''
            ];
        }

        $responseData['data']['response'] = $response;
        // $responseData['data']['request']                = $modelLead;
        $responseData = $apiLog->endApiLog($responseData);

        if (isset($response['error']) && $response['error']) {
            $json = @json_encode($response['error']);
            if (isset($response['error_code']) && $response['error_code']) {
                $error_code = $response['error_code'];
            } else {
                $error_code = 0;
            }
            throw new UnprocessableEntityHttpException($json, $error_code);
        }

        return $responseData;
    }


    /**
     * @api {post} /v1/communication/sms Communication SMS
     * @apiVersion 0.1.0
     * @apiName CommunicationSms
     * @apiGroup Communication
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization    Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *      "type": "update_sms_status",
     *      "sq_id": 127,
     *      "sq_status_id": 5,
     *  }
     *
     *
     * @return array
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     * @throws \yii\web\BadRequestHttpException
     */

    public function actionSms(): array
    {
        $this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);

        //$action = Yii::$app->request->post('action');
        $type = Yii::$app->request->post('type');

        /*if(!$action) {
            throw new NotFoundHttpException('Not found action', 1);
        }*/

        if(!$type) {
            throw new NotFoundHttpException('Not found type', 1);
        }


        switch ($type) {
            case self::TYPE_UPDATE_SMS_STATUS : $response = $this->updateSmsStatus();
                break;
            case self::TYPE_NEW_SMS_MESSAGES_RECEIVED : $response = $this->newSmsMessagesReceived();
                break;
            default: throw new BadRequestHttpException('Invalid type', 2);
        }

        $responseData = [];

        if (isset($response['error']) && $response['error']) {

        } else {
            $responseData = [
                'status'    => 200,
                'name'      => 'Success',
                'code'      => 0,
                'message'   => ''
            ];
        }

        $responseData['data']['response'] = $response;
        // $responseData['data']['request']                = $modelLead;
        $responseData = $apiLog->endApiLog($responseData);

        if (isset($response['error']) && $response['error']) {
            $json = @json_encode($response['error']);
            if (isset($response['error_code']) && $response['error_code']) {
                $error_code = $response['error_code'];
            } else {
                $error_code = 0;
            }
            throw new UnprocessableEntityHttpException($json, $error_code);
        }

        return $responseData;
    }


    /**
     * @api {post} /v1/communication/voice Communication Voice
     * @apiVersion 0.1.0
     * @apiName CommunicationVoice
     * @apiGroup Communication
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization    Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *      "type": "update_sms_status"
     *  }
     *
     *
     * @return array
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     * @throws \yii\web\BadRequestHttpException
     */

    public function actionVoice(): array
    {
        $this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);

        //$action = Yii::$app->request->post('action');
        $type = Yii::$app->request->post('type');

        /*if(!$action) {
            throw new NotFoundHttpException('Not found action', 1);
        }*/


//        [
//            'c_id' => '5'
//            'c_call_status' => 'completed'
//            'c_project_id' => '2'
//            'callData' => [
//                'ApiVersion' => '2010-04-01'
//                'Called' => 'sip:alex.connor@kivork.sip.us1.twilio.com'
//                'CallStatus' => 'completed'
//                'Duration' => '1'
//                'From' => 'admin'
//                'CallerCountry' => 'CF'
//                'Direction' => 'outbound-api'
//                'Timestamp' => 'Wed, 23 Jan 2019 16:36:00 +0000'
//                'CallDuration' => '18'
//                'CallbackSource' => 'call-progress-events'
//                'AccountSid' => 'AC10f3c74efba7b492cbd7dca86077736c'
//                'SipCallId' => 'd39486fe63a4c8de6c946994f3c9f17f@0.0.0.0'
//                'CallerCity' => ''
//                'SipResponseCode' => '200'
//                'CallerState' => ''
//                'Caller' => 'admin'
//                'FromCountry' => 'CF'
//                'FromCity' => ''
//                'SequenceNumber' => '3'
//                'CallSid' => 'CA35e3633dd15dc2d2c50f8d62d9f94187'
//                'To' => 'sip:alex.connor@kivork.sip.us1.twilio.com'
//                'FromZip' => ''
//                'CallerZip' => ''
//                'FromState' => ''
//            ]
//            'action' => 'update'
//            'type' => 'voip'
//        ]

        $post = Yii::$app->request->post();

        $response = $post;

        if($type == self::TYPE_VOIP_INCOMING) {


                    //['type'] =
                        //['call_id']
                /*[call][
                    'Called' => '+15596489977'
                   'ToState' => 'CA'
                   'CallerCountry' => 'US'
                   'Direction' => 'inbound'
                   'CallerState' => 'CA'
                   'ToZip' => '93618'
                   'CallSid' => 'CAda14121e342c1eb9835b3131537283d1'
                   'To' => '+15596489977'
                   'CallerZip' => '94949'
                   'ToCountry' => 'US'
                   'ApiVersion' => '2010-04-01'
                   'CalledZip' => '93618'
                   'CalledCity' => 'DINUBA'
                   'CallStatus' => 'ringing'
                   'From' => '+14154834499'
                   'AccountSid' => 'AC10f3c74efba7b492cbd7dca86077736c'
                   'CalledCountry' => 'US'
                   'CallerCity' => 'IGNACIO'
                   'ApplicationSid' => 'APd65ba6826de6314e0780220d89fc6cde'
                   'Caller' => '+14154834499'
                   'FromCountry' => 'US'
                   'ToCity' => 'DINUBA'
                   'FromCity' => 'IGNACIO'
                   'CalledState' => 'CA'
                   'FromZip' => '94949'
                   'FromState' => 'CA'
                ]*/


            Yii::info(VarDumper::dumpAsString($post), 'info\API:CommunicationController:actionVoice:TYPE_VOIP_INCOMING');



            if(isset($post['call']) && $post['call']) {

                //Yii::info('Detect - Call', 'info\API:CommunicationController:actionVoice:DetectCall - 0');

                $client_phone_number = null;
                $agent_phone_number = null;

                if (isset($post['call']['From']) && $post['call']['From']) {
                    $client_phone_number = $post['call']['From'];
                }

                if (isset($post['call']['To']) && $post['call']['To']) {
                    $agent_phone_number = $post['call']['Called'];
                }

                if (!$client_phone_number) {
                    $response['error'] = 'Not found Call From (Client phone number)';
                    $response['error_code'] = 10;
                }

                if (!$agent_phone_number) {
                    $response['error'] = 'Not found Call Called (Agent phone number)';
                    $response['error_code'] = 11;
                }


                $isRedirectCall = true;

                $call_user_id = null;
                $call_sip_id = null;
                $call_project_id = null;

                //$upp = UserProjectParams::find()->where(['upp_phone_number' => $agent_phone_number])->orWhere(['upp_tw_phone_number' => $agent_phone_number])->one();
                $upp = UserProjectParams::find()->where(['upp_tw_phone_number' => $agent_phone_number])->one();


                $user = null;


                if($upp && $user = $upp->uppUser) {

                    $call_user_id = (int) $upp->upp_user_id;
                    $call_sip_id = $upp->upp_tw_sip_id;
                    $call_project_id = (int) $upp->upp_project_id;

                    //Yii::info('Detect - User ('.$user->username.') Id: '.$user->id.', phone: ' . $agent_phone_number, 'info\API:CommunicationController:actionVoice:UserProjectParams - 1');

                    if($user->isOnline()) {
                        if($user->isCallStatusReady()) {
                            if($user->isCallFree()) {
                                $isRedirectCall = false;
                                Yii::info('DIRECT - User ('.$user->username.') Id: '.$user->id.', phone: ' . $agent_phone_number, 'info\API:CommunicationController:actionVoice:Direct - 2');

                            } else {
                                Yii::info('Call Occupied - User ('.$user->username.') Id: '.$user->id.', phone: ' . $agent_phone_number, 'info\API:CommunicationController:actionVoice:isCallFree');
                                Notifications::create($user->id, 'Missing Call from '.$client_phone_number.' [Occupied]', 'Missing Call from ' . $client_phone_number .' to '.$agent_phone_number . "\r\n Reason: Agent Occupied", Notifications::TYPE_WARNING, true);
                                Notifications::socket($user->id, null, 'getNewNotification', [], true);
                            }
                        } else {
                            Yii::info('Call Status not Ready - User ('.$user->username.') Id: '.$user->id.', phone: ' . $agent_phone_number, 'info\API:CommunicationController:actionVoice:isCallStatusReady');
                            Notifications::create($user->id, 'Missing Call from '.$client_phone_number.' [not Ready]', 'Missing Call from ' . $client_phone_number .' to '.$agent_phone_number . "\r\n Reason: Call Status not Ready", Notifications::TYPE_WARNING, true);
                            Notifications::socket($user->id, null, 'getNewNotification', [], true);
                        }
                    } else {
                        Yii::info('Offline - User ('.$user->username.') Id: '.$user->id.', phone: ' . $agent_phone_number, 'info\API:CommunicationController:actionVoice:isOnline');
                        Notifications::create($user->id, 'Missing Call from '.$client_phone_number.' [Offline]', 'Missing Call from ' . $client_phone_number .' to '.$agent_phone_number . "\r\n Reason: Agent offline", Notifications::TYPE_WARNING, true);
                        Notifications::socket($user->id, null, 'getNewNotification', [], true);
                    }
                }


                if($isRedirectCall && $call_user_id && $call_project_id) {

                    $call_sip_id = null;

                    Yii::info('isRedirectCall - call_user_id ('.$call_user_id.'), call_project_id: '. $call_project_id, 'info\API:CommunicationController:actionVoice:Redirect - 3');
                    $usersForCall = Employee::getAgentsForCall($call_user_id, $call_project_id);

                    Yii::info('Redirect usersForCall: ' . VarDumper::dumpAsString($usersForCall), 'info\API:CommunicationController:actionVoice:getAgentsForCall - 4');

                    if($usersForCall) {
                        foreach ($usersForCall as $userForCall) {
                            $upp = UserProjectParams::find()->where(['upp_user_id' => $userForCall['tbl_user_id'], 'upp_project_id' => $call_project_id])->one();

                            if($upp) {

                                $call_user_id = (int) $upp->upp_user_id;
                                $call_sip_id = $upp->upp_tw_sip_id;
                                if($upp->upp_tw_phone_number) {
                                    $agent_phone_number = $upp->upp_tw_phone_number;
                                }

                                Yii::info('Redirected Call: call_user_id: '.$call_user_id.', call_sip_id: '.$call_sip_id.', agent_phone_number: '.$agent_phone_number, 'info\API:CommunicationController:actionVoice:UserProjectParams - 5');

                                break;
                            }
                        }
                    }
                    /*
                     *    0 => [
                                'tbl_user_id' => '167'
                                'tbl_call_status_id' => '2'
                                'tbl_last_call_status' => null
                                'tbl_sip_id' => null
                                'tbl_calls_count' => '0'
                            ]
                     */
                }

                $generalLineProject = '';

                if($call_project_id && !$call_sip_id) {

                    $project = Project::findOne($call_project_id);
                    if($project && $project->contactInfo && $project->contactInfo->phone) {
                        $generalLineProject = str_replace(' ', '', $project->contactInfo->phone);
                        $generalLineProject = str_replace('-', '', $generalLineProject);
                        if(isset($generalLineProject[0]) && $generalLineProject[0] !== '+') {
                            $generalLineProject = '+' . $generalLineProject;
                        }

                        Yii::info('Redirected to General Line : call_project_id: '.$call_project_id.', generalLine: '.$generalLineProject, 'info\API:CommunicationController:actionVoice:GeneralLine - 6');
                    }
                }

                /*if($isRedirectCall) {

                }*/


                if ($call_project_id && $call_user_id) {

                    $call = new Call();

                    $call->c_call_sid = $post['call']['CallSid'] ?? null;
                    $call->c_account_sid = $post['call']['AccountSid'] ?? null;
                    $call->c_call_type_id = Call::CALL_TYPE_IN;
                    $call->c_call_status = $post['call']['CallStatus'] ?? Call::CALL_STATUS_RINGING;
                    $call->c_com_call_id = $post['call_id'] ?? null;
                    $call->c_direction = $post['call']['Direction'] ?? null;
                    $call->c_project_id = $call_project_id;
                    $call->c_is_new = true;
                    $call->c_api_version = $post['call']['ApiVersion'] ?? null;
                    $call->c_created_dt = date('Y-m-d H:i:s');

                    $call->c_from = $client_phone_number;
                    $call->c_sip = $call_sip_id;
                    $call->c_to = $call_sip_id ? $agent_phone_number : $generalLineProject;

                    if($call_sip_id) {
                        $call->c_created_user_id = $call_user_id;
                    }

                    if(!$call->save()) {
                        Yii::error(VarDumper::dumpAsString($call->errors), 'API:CommunicationController:actionVoice:Call:save');
                    } else {

                        $data = [];
                        $data['client_name'] = 'Noname';
                        $data['client_id'] = null;
                        $data['last_lead_id'] = null;
                        $data['client_emails'] = [];
                        $data['client_phones'] = [];


                        $data['client_count_calls'] = 0;
                        $data['client_count_sms'] = 0;
                        $data['client_created_date'] = '';
                        $data['client_last_activity'] = '';

                        $clientPhone = ClientPhone::find()->where(['phone' => $client_phone_number])->one();

                        if($clientPhone && $client = $clientPhone->client) {
                            $data['client_name'] = $client->full_name;
                            $data['client_id'] = $clientPhone->client_id;
                            $data['client_created_date'] = Yii::$app->formatter->asDate(strtotime($client->created));

                            $lead = Lead::find()->select(['id'])->where(['client_id' => $clientPhone->client_id])->orderBy(['id' => SORT_DESC])->limit(1)->one();
                            if($lead) {
                                $data['last_lead_id'] = $lead->id;
                                $data['client_last_activity'] = Yii::$app->formatter->asDate(strtotime($client->created));
                                $call->c_lead_id = $lead->id;
                                $call->save();
                            }

                            /*$data['client_count_calls'] = Call::find()->where(['c_from' => $client_phone_number])->orWhere(['c_to' => $client_phone_number])->count();
                            $data['client_count_sms'] = Sms::find()->where(['s_phone_from' => $client_phone_number])->orWhere(['s_phone_to' => $client_phone_number])->count();


                            if($client->clientEmails) {
                               foreach ($client->clientEmails as $email) {
                                   $data['client_emails'][] = $email->email;
                               }
                            }

                            if($client->clientPhones) {
                                foreach ($client->clientPhones as $phone) {
                                    $data['client_phones'][] = $phone->phone;
                                }
                            }*/
                        }

                        $data['client_phone'] = $client_phone_number;
                        $data['agent_phone'] = $agent_phone_number;

                        //$data['post'] = $post;

                        $data['status'] = $call->c_call_status;

                        //Notifications::create($call->c_created_user_id, 'New Call from '.$call->c_from. ' ('.$data['client_name'].')', 'Call from ' . $call->c_from .' ('.$data['client_name'].') to '.$call->c_to, Notifications::TYPE_INFO, true);
                        if($call->c_created_user_id) {
                            Notifications::socket($call->c_created_user_id, $lead_id = null, 'incomingCall', $data, true);
                        }

                        //Notifications::socket(null, $call->c_lead_id, 'callUpdate', ['status' => $call->c_call_status, 'duration' => $call->c_call_duration, 'snr' => $call->c_sequence_number], true);
                    }

                    /*if ($call->c_lead_id) {
                        Notifications::create($user_id, 'New SMS '.$sms->s_phone_from, 'SMS from ' . $sms->s_phone_from .' ('.$clientName.') to '.$sms->s_phone_to.' <br> '.nl2br(Html::encode($sms->s_sms_text))
                            . ($lead_id ? '<br>Lead ID: '.$lead_id : ''), Notifications::TYPE_INFO, true);
                        //Notifications::socket(null, $call->c_lead_id, 'callUpdate', ['status' => $call->c_call_status, 'duration' => $call->c_call_duration, 'snr' => $call->c_sequence_number], true);
                    }*/


                    //if ($call_sip_id) {

                        $response['agent_sip'] = $call_sip_id;
                        $response['agent_phone_number'] = $agent_phone_number;
                        $response['client_phone_number'] = $client_phone_number;
                        $response['general_phone_number'] = $generalLineProject;

                    /*} else {
                        $response['error'] = 'Agent SIP account is empty';
                        $response['error_code'] = 14;
                    }*/
                } else {
                    $response['error'] = 'Not found call_project_id or call_user_id';
                    $response['error_code'] = 13;
                }
            } else {
                $response['error'] = 'Not found "call" array';
                $response['error_code'] = 12;
            }



            /*$statuses = ['initiated', 'ringing', 'in-progress', 'completed'];
            $user_id = Yii::$app->user->id;
            $n = 0;


            $data = [];
            $data['client_name'] = 'Alexandr Test';
            $data['client_id'] = 345;
            $data['client_phone'] = '+3738956478';
            $data['last_lead_id'] = 34567;

            foreach ($statuses as $status) {
                sleep(random_int(3, 5));
                $data['status'] = $status;
                $n++;
                Notifications::socket($user_id, $lead_id = null, 'incomingCall', $data, true);
                echo '<br>'.$status;
            }*/


        } elseif($type == self::TYPE_VOIP_RECORD) {

            if (isset($post['callData']['CallSid']) && $post['callData']['CallSid']) {
                $call = Call::find()->where(['c_call_sid' => $post['callData']['CallSid']])->one();
                if ($call) {

                    if($post['callData']['RecordingUrl']) {
                        $call->c_recording_url = $post['callData']['RecordingUrl'];
                        $call->c_recording_duration = $post['callData']['RecordingDuration'];
                        $call->c_recording_sid = $post['callData']['RecordingSid'];
                        $call->c_updated_dt = date('Y-m-d H:i:s');


                        if(!$call->save()) {
                            Yii::error(VarDumper::dumpAsString($call->errors), 'API:CommunicationController:actionVoice:Call1:save');
                        }
                        if ($call->c_lead_id) {

                            if($call->c_created_user_id) {
                                Notifications::create($call->c_created_user_id, 'Call Recording Completed  from ' . $call->c_from . ' to ' . $call->c_to . ' <br>Lead ID: ' . $call->c_lead_id , Notifications::TYPE_INFO, true);
                            }
                            Notifications::socket(null, $call->c_lead_id, 'recordingUpdate', ['url' => $call->c_recording_url], true);
                        }
                    }
                }
            }

        } else {
            if (isset($post['callData']['CallSid']) && $post['callData']['CallSid']) {
                $call = Call::find()->where(['c_call_sid' => $post['callData']['CallSid']])->one();
                if ($call) {

                    $call->c_call_status = $post['callData']['CallStatus'] ?? '';
                    $call->c_sequence_number = $post['callData']['SequenceNumber'] ?? 0;

                    if (isset($post['callData']['CallDuration'])) {
                        $call->c_call_duration = (int) $post['callData']['CallDuration'];
                    }


                    $call->c_updated_dt = date('Y-m-d H:i:s');
                    if(!$call->save()) {
                        Yii::error(VarDumper::dumpAsString($call->errors), 'API:CommunicationController:actionVoice:Call2:save');
                    }
                    if ($call->c_lead_id) {
                        /*Notifications::create($user_id, 'New SMS '.$sms->s_phone_from, 'SMS from ' . $sms->s_phone_from .' ('.$clientName.') to '.$sms->s_phone_to.' <br> '.nl2br(Html::encode($sms->s_sms_text))
                            . ($lead_id ? '<br>Lead ID: '.$lead_id : ''), Notifications::TYPE_INFO, true);*/
                        Notifications::socket(null, $call->c_lead_id, 'callUpdate', ['status' => $call->c_call_status, 'duration' => $call->c_call_duration, 'snr' => $call->c_sequence_number], true);
                    }

                    if($call->c_created_user_id) {
                        Notifications::socket($call->c_created_user_id, $lead_id = null, 'incomingCall', ['status' => $call->c_call_status, 'duration' => $call->c_call_duration, 'snr' => $call->c_sequence_number], true);
                    }

                }
            }
        }



        $responseData = [];

        if (isset($response['error']) && $response['error']) {

        } else {
            $responseData = [
                'status'    => 200,
                'name'      => 'Success',
                'code'      => 0,
                'message'   => ''
            ];
        }

        $responseData['data']['response'] = $response;
        // $responseData['data']['request']                = $modelLead;
        $responseData = $apiLog->endApiLog($responseData);

        if (isset($response['error']) && $response['error']) {
            $json = @json_encode($response['error']);
            if (isset($response['error_code']) && $response['error_code']) {
                $error_code = $response['error_code'];
            } else {
                $error_code = 0;
            }
            throw new UnprocessableEntityHttpException($json, $error_code);
        }

        return $responseData;
    }


    /**
     * @return mixed
     */
    private function updateEmailStatus()
    {
        $eq_id = (int) Yii::$app->request->post('eq_id');
        $eq_status_id = (int) Yii::$app->request->post('eq_status_id');
        // $eq_project_id = Yii::$app->request->post('eq_project_id');

        try {

            if(!$eq_id) {
                throw new NotFoundHttpException('Not found eq_id', 11);
            }

            if(!$eq_status_id) {
                throw new NotFoundHttpException('Not found eq_status_id', 12);
            }

            $email = Email::findOne(['e_communication_id' => $eq_id]);
            if($email) {

                if($eq_status_id > 0) {
                    $email->e_status_id = $eq_status_id;
                    if($eq_status_id === Email::STATUS_DONE) {
                        $email->e_status_done_dt = date('Y-m-d H:i:s');
                    }

                    if(!$email->save()) {
                        Yii::error(VarDumper::dumpAsString($email->errors), 'API:Communication:updateEmailStatus:Email:save');
                    }
                }

                $response['email'] = $email->e_id;
            } else {
                $response['error'] = 'Not found Communication ID ('.$eq_id.')';
                $response['error_code'] = 13;
            }


        } catch (\Throwable $e) {
            Yii::error($e->getTraceAsString(), 'API:Communication:updateEmailStatus:Email:try');
            $message = $this->debug ? $e->getTraceAsString() : $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
            $response['error'] = $message;
            $response['error_code'] = 15;
        }

        return $response;
    }


    /**
     * @return mixed
     */
    private function updateSmsStatus()
    {
        $sq_id = (int) Yii::$app->request->post('sq_id');
        $sq_status_id = (int) Yii::$app->request->post('sq_status_id');

        //$sq_price = Yii::$app->request->post('sq_price');

        $smsParams = Yii::$app->request->post('sms');

        // $sq_project_id = Yii::$app->request->post('sq_project_id');

        try {

            if(!$sq_id) {
                throw new NotFoundHttpException('Not found sq_id', 11);
            }

            if(!$sq_status_id) {
                throw new NotFoundHttpException('Not found sq_status_id', 12);
            }

            $sms = Sms::findOne(['s_communication_id' => $sq_id]);
            if($sms) {

                if($sq_status_id > 0) {
                    $sms->s_status_id = $sq_status_id;
                    if($sq_status_id === Sms::STATUS_DONE) {
                        $sms->s_status_done_dt = date('Y-m-d H:i:s');
                    }


                    if($smsParams) {
                        if(isset($smsParams['sq_tw_price']) && $smsParams['sq_tw_price']) {
                            $sms->s_tw_price = (float) $smsParams['sq_tw_price'];
                        }

                        if(isset($smsParams['sq_tw_num_segments']) && $smsParams['sq_tw_num_segments']) {
                            $sms->s_tw_num_segments = (int) $smsParams['sq_tw_num_segments'];
                        }
                    }

                    if(!$sms->save()) {
                        Yii::error(VarDumper::dumpAsString($sms->errors), 'API:Communication:updateSmsStatus:Sms:save');
                    }
                }

                $response['sms'] = $sms->s_id;
            } else {
                $response['error'] = 'Not found Communication ID ('.$sq_id.')';
                $response['error_code'] = 13;
            }


        } catch (\Throwable $e) {
            Yii::error($e->getTraceAsString(), 'API:Communication:updateEmailStatus:Email:try');
            $message = $this->debug ? $e->getTraceAsString() : $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
            $response['error'] = $message;
            $response['error_code'] = 15;
        }

        return $response;
    }


    /**
     * @param null $last_id
     * @return array
     */
    private function newEmailMessagesReceived($last_id = NULl): array
    {
        $response = [];
        try {

            $filter = [];
            $dateTime = null;
            if(NULL === $last_id) {

                $lastEmail = Email::find()->where('e_inbox_email_id > 0')->orderBy(['e_inbox_email_id' => SORT_DESC])->one();

                if ($lastEmail) {
                    //$filter['last_dt'] = $lastEmail->e_inbox_created_dt;
                    $filter['last_id'] = $lastEmail->e_inbox_email_id + 1;
                } else {
                    $filter['last_id'] = 18100;
                }
            } else {
                $filter['last_id'] = (int)$last_id;

                $checkLastEmail = Email::find()->where('e_inbox_email_id = ' . $filter['last_id'] )->one();
                if($checkLastEmail) {
                    $response[] = 'Last ID ' . $filter['last_id'] . ' Exists';
                    return $response;
                }

            }

            $filter['limit'] = 20;

            $mailList = [];
            $mails = UserProjectParams::find()->select(['DISTINCT(upp_email)'])->andWhere(['!=', 'upp_email', ''])->asArray()->all();
            if($mails) {
                $mailList = ArrayHelper::getColumn($mails,'upp_email');
            }
            $filter['mail_list'] = $mailList;

            // push job
            $job = new ReceiveEmailsJob();
            $job->last_email_id = $filter['last_id'];
            $data = [
                'last_email_id' => $filter['last_id'],
                'mail_list' => $filter['mail_list'],
                'limit' => $filter['limit'],
            ];
            $job->request_data = $data;
            /** @var Queue $queue */
            $queue = \Yii::$app->queue_email_job;
            $jobId = $queue->push($job);
            $response = [
                'job_id' => $jobId,
                'last_id' => $filter['last_id'],
            ];

        } catch (\Throwable $e) {
            Yii::error($e->getTraceAsString(), 'API:Communication:newEmailMessagesReceived:Email:try');
            $message = $this->debug ? $e->getTraceAsString() : $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
            $response['error'] = $message;
            $response['error_code'] = 15;
        }

        return $response;
    }


    /**
     * @return array
     */
    private function newSmsMessagesReceived(): array
    {
        $response = [];

        $smsItem = Yii::$app->request->post();

        if(!\is_array($smsItem)) {
            $response['error'] = 'Sales: Invalid POST request (array)';
            $response['error_code'] = 16;
        }

        if(!isset($smsItem['si_id'])) {
            $response['error'] = 'Sales: Invalid POST request - not found (si_id)';
            $response['error_code'] = 17;
        }

        if(isset($response['error']) && $response['error']) {
            return $response;
        }

        try {

                /*
                 *  * @property int $si_id
                 * @property string $si_phone_to
                 * @property string $si_phone_from
                 * @property string $si_sms_text
                 * @property int $si_project_id
                 * @property bool $si_deleted
                 * @property string $si_sent_dt
                 * @property string $si_created_dt
                 * @property string $si_updated_dt
                 * @property string $si_message_sid
                 * @property int $si_num_segments
                 * @property string $si_to_country
                 * @property string $si_to_state
                 * @property string $si_to_city
                 * @property string $si_to_zip
                 * @property string $si_from_country
                 * @property string $si_from_city
                 * @property string $si_from_state
                 * @property string $si_from_zip
                 */

                    $sms = new Sms();
                    $sms->s_type_id = Sms::TYPE_INBOX;
                    $sms->s_status_id = Sms::STATUS_DONE;
                    $sms->s_is_new = true;

                    $sms->s_status_done_dt = isset($smsItem['si_sent_dt']) ? date('Y-m-d H:i:s', strtotime($smsItem['si_sent_dt'])) : null;

                    $sms->s_communication_id = $smsItem['si_id'] ?? null;

                    $sms->s_phone_to = $smsItem['si_phone_to'];
                    $sms->s_phone_from = $smsItem['si_phone_from'];
                    $sms->s_project_id = $smsItem['si_project_id'] ?? null;
                    $sms->s_sms_text = $smsItem['si_sms_text'];
                    $sms->s_created_dt = $smsItem['si_created_dt'];

                    $sms->s_tw_message_sid = $smsItem['si_message_sid'] ?? null;
                    $sms->s_tw_num_segments = $smsItem['si_num_segments'] ?? null;

                    $sms->s_tw_to_country = $smsItem['si_to_country'] ?? null;
                    $sms->s_tw_to_state = $smsItem['si_to_state'] ?? null;
                    $sms->s_tw_to_city = $smsItem['si_to_city'] ?? null;
                    $sms->s_tw_to_zip = $smsItem['si_to_zip'] ?? null;

                    $sms->s_tw_from_country = $smsItem['si_from_country'] ?? null;
                    $sms->s_tw_from_city = $smsItem['si_from_city'] ?? null;
                    $sms->s_tw_from_state = $smsItem['si_from_state'] ?? null;
                    $sms->s_tw_from_zip = $smsItem['si_from_zip'] ?? null;

                    $lead_id = $sms->detectLeadId();


                    if($lead_id) {
                        $lead = Lead::findOne($lead_id);
                        if($lead) {
                            $sms->s_project_id = $lead->project_id;
                        }
                        Yii::info('SMS Detected LeadId '.$lead_id.' from '.$sms->s_phone_from, 'info\API:Communication:newSmsMessagesReceived:Sms');
                    }


                    if(!$sms->save()) {
                        Yii::error(VarDumper::dumpAsString($sms->errors), 'API:Communication:newSmsMessagesReceived:Sms:save');
                        $response['error_code'] = 12;
                        throw new \Exception('Error save SMS data ' . VarDumper::dumpAsString($sms->errors));
                    }


                    //Notifications::create(Yii::$app->user->id, 'Test '.date('H:i:s'), 'Test message <h2>asdasdasd</h2>', Notifications::TYPE_SUCCESS, true);


                    $users = $sms->getUsersIdByPhone();

                    $clientPhone = ClientPhone::find()->where(['phone' => $sms->s_phone_from])->orderBy(['id' => SORT_DESC])->limit(1)->one();
                    if($clientPhone) {
                        $clientName = $clientPhone->client ? $clientPhone->client->full_name : '-';
                    } else {
                        $clientName = '-';
                    }

                    $user_id = 0;

                    if($users) {
                        foreach ($users as $user_id) {

                            Notifications::create($user_id, 'New SMS '.$sms->s_phone_from, 'SMS from ' . $sms->s_phone_from .' ('.$clientName.') to '.$sms->s_phone_to.' <br> '.nl2br(Html::encode($sms->s_sms_text))
                            . ($lead_id ? '<br>Lead ID: '.$lead_id : ''), Notifications::TYPE_INFO, true);
                            Notifications::socket($user_id, null, 'getNewNotification', ['sms_id' => $sms->s_id], true);
                        }
                    }

                    if($user_id > 0) {
                        $sms->s_created_user_id = $user_id;
                        $sms->save();
                    }

                    if($lead_id) {
                        Notifications::socket(null, $lead_id, 'updateCommunication', ['sms_id' => $sms->s_id], true);
                    }

                    $response = $sms->attributes;

        } catch (\Throwable $e) {
            Yii::error($e->getTraceAsString(), 'API:Communication:newSmsMessagesReceived:Sms:try');
            $message = $this->debug ? $e->getTraceAsString() : $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
            $response['error'] = $message;
            if(!isset($response['error_code']) || !$response['error_code']) {
                $response['error_code'] = 15;
            }
        }

        return $response;
    }
}
