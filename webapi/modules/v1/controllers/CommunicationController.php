<?php
namespace webapi\modules\v1\controllers;

use common\components\jobs\CallQueueJob;
use common\models\ApiLog;
use common\models\Call;
use common\models\CallUserGroup;
use common\models\ClientPhone;
use common\models\Department;
use common\models\DepartmentPhoneProject;
use common\models\Email;
use common\models\Employee;
use common\models\Lead;
use common\models\Notifications;
use common\models\Sms;
use common\models\Sources;
use common\models\UserProjectParams;
use Twilio\TwiML\VoiceResponse;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;
use common\components\ReceiveEmailsJob;
use yii\queue\Queue;

/**
 * Class CommunicationController
 *
 */
class CommunicationController extends ApiBaseController
{

    public const TYPE_VOIP_RECORD       = 'voip_record';
    public const TYPE_VOIP_INCOMING     = 'voip_incoming';
    public const TYPE_VOIP_GATHER       = 'voip_gather';
    public const TYPE_VOIP_CLIENT       = 'voip_client';
    public const TYPE_VOIP_FINISH       = 'voip_finish';

    public const TYPE_UPDATE_EMAIL_STATUS = 'update_email_status';
    public const TYPE_UPDATE_SMS_STATUS = 'update_sms_status';

    public const TYPE_NEW_EMAIL_MESSAGES_RECEIVED = 'new_email_messages_received';
    public const TYPE_NEW_SMS_MESSAGES_RECEIVED = 'new_sms_messages_received';

    public const TYPE_SMS_FINISH        = 'sms_finish';

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

        $type = Yii::$app->request->post('type');
        $last_id = Yii::$app->request->post('last_email_id', NULL);

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

        $responseData = $this->getResponseData($response, $apiLog);
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
        $type = Yii::$app->request->post('type');

        if(!$type) {
            throw new NotFoundHttpException('Not found type', 1);
        }

        switch ($type) {
            case self::TYPE_UPDATE_SMS_STATUS :
                $response = $this->updateSmsStatus();
                break;
            case self::TYPE_NEW_SMS_MESSAGES_RECEIVED :
                $response = $this->newSmsMessagesReceived();
                break;
            case self::TYPE_SMS_FINISH :
                $response = $this->smsFinish();
                break;
            default:
                throw new BadRequestHttpException('Invalid type', 2);
        }

        $responseData = $this->getResponseData($response, $apiLog);
        return $responseData;
    }




    /**
     *
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
     * @return array
     * @throws BadRequestHttpException
     * @throws UnprocessableEntityHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionVoice(): array
    {
        $this->checkPost();
        $type = Yii::$app->request->post('type');

        $apiLog = $this->startApiLog($this->action->uniqueId . ($type ? '/' . $type : ''));
        $post = Yii::$app->request->post();

        switch ($type) {
            case self::TYPE_VOIP_INCOMING:
            case self::TYPE_VOIP_GATHER:
                $response = $this->voiceIncoming($post, $type);
                break;
            case self::TYPE_VOIP_CLIENT:
                $response = $this->voiceClient($post);
                break;
            case self::TYPE_VOIP_FINISH:
                $response = $this->voiceFinish($post);
                break;
            case self::TYPE_VOIP_RECORD:
                $response = $this->voiceRecord($post);
                break;
            default:
                $response = $this->voiceDefault($post);
        }

        $responseData = $this->getResponseData($response, $apiLog);
        return $responseData;
    }


    /**
     * @param array $post
     * @param string $type
     * @return array
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    private function voiceIncoming(array $post, string $type): array
    {
        $response = [];

        // Yii::info(VarDumper::dumpAsString($post), 'info\API:Communication:voiceIncoming');

        $clientPhone = null;

        $postCall = $post['call'] ?? [];

        // $ciscoPhoneNumber = \Yii::$app->params['global_phone'];

        if ($postCall) {

            $client_phone_number = null;
            $incoming_phone_number = null;

            $callSid = $postCall['CallSid'] ?? null;
            $parentCallSid = $postCall['ParentCallSid'] ?? null;

            $postCall['c_com_call_id'] = $post['call_id'] ?? null;
            $client_phone_number = $postCall['From'] ?? null;
            $incoming_phone_number = $postCall['Called'] ?? null;


            if (!$client_phone_number) {
                $response['error'] = 'Not found Call From (Client phone number)';
                $response['error_code'] = 10;
            }

            if (!$incoming_phone_number) {
                $response['error'] = 'Not found Call Called (Agent phone number)';
                $response['error_code'] = 11;
            }

            //$clientPhone = ClientPhone::find()->where(['phone' => $client_phone_number])->orderBy(['id' => SORT_DESC])->limit(1)->one();

            $departmentPhone = DepartmentPhoneProject::find()->where(['dpp_phone_number' => $incoming_phone_number, 'dpp_enable' => true])->limit(1)->one();

            if ($departmentPhone) {

                $project = $departmentPhone->dppProject;
                $source = $departmentPhone->dppSource;
                if ($project && !$source) {
                    $source = Sources::find()->where(['project_id' => $project->id, 'default' => true])->one();
                    if ($source) {
                        $departmentPhone->dpp_source_id = $source->id;
                    }
                }

                $call_project_id = $departmentPhone->dpp_project_id;
                $call_dep_id = $departmentPhone->dpp_dep_id;

                $ivrEnable = (bool)$departmentPhone->dpp_ivr_enable;

                $callModel = $this->findOrCreateCall($callSid, $parentCallSid, $postCall, $call_project_id,
                    $call_dep_id);

                if ($departmentPhone->dugUgs) {
                    foreach ($departmentPhone->dugUgs as $userGroup) {
                        $cug = new CallUserGroup();
                        $cug->cug_ug_id = $userGroup->ug_id;
                        $cug->cug_c_id = $callModel->c_id;
                        //$cug->link('cugUg', $callModel);
                        if (!$cug->save()) {
                            Yii::error(VarDumper::dumpAsString($cug->errors),
                                'API:Communication:voiceIncoming:CallUserGroup:save');
                        }
                    }
                }

                $callModel->c_source_type_id = Call::SOURCE_GENERAL_LINE;

                if ($departmentPhone->dpp_source_id) {
                    $callModel->c_source_type_id = $departmentPhone->dpp_source_id;
                }

                if ($ivrEnable) {
                    $ivrSelectedDigit = isset($postCall['Digits']) ? (int)$postCall['Digits'] : null;
                    $ivrStep = (int)Yii::$app->request->get('step', 1);
                    return $this->ivrService($callModel, $departmentPhone, $ivrStep, $ivrSelectedDigit);
                }

                $response['error'] = 'Not enable IVR';
                $response['error_code'] = 13;

            } else {

                $upp = UserProjectParams::find()->where(['upp_tw_phone_number' => $incoming_phone_number])->limit(1)->one();
                if ($upp) {

                    if ($upp->upp_dep_id) {
                        $call_dep_id = $upp->upp_dep_id;
                    } elseif ($upp->uppUser && $upp->uppUser->userDepartments && isset($upp->uppUser->userDepartments[0])) {
                        $call_dep_id = $upp->uppUser->userDepartments[0]->ud_dep_id;

                        /*foreach ($upp->uppUser->userDepartments as $userDepartment) {
                            $call_dep_id = $userDepartment->ud_dep_id;
                            break;
                        }*/
                    } else {
                        $call_dep_id = null;
                    }

                    $callModel = $this->findOrCreateCall($callSid, $parentCallSid, $postCall, $upp->upp_project_id,
                        $call_dep_id);
                    $callModel->c_source_type_id = Call::SOURCE_DIRECT_CALL;

                    $user = $upp->uppUser;

                    if ($user) {
                        if ($user->isOnline()) {
                            // Yii::info('DIRECT CALL - User (' . $user->username . ') Id: ' . $user->id . ', phone: ' . $incoming_phone_number, 'info\API:Communication:Incoming:DirectCall');
                            return $this->createDirectCall($callModel, $user);
                        }

                        Yii::info('Offline - User (' . $user->username . ') Id: ' . $user->id . ', phone: ' . $incoming_phone_number,
                            'info\API:Communication:Incoming:Offline');
                        Notifications::create($user->id, 'Missing Call [Offline]',
                            'Missing Call from ' . $client_phone_number . ' to ' . $incoming_phone_number . "\r\n Reason: Agent offline",
                            Notifications::TYPE_WARNING, true);
                        Notifications::socket($user->id, null, 'getNewNotification', [], true);
                        $callModel->c_source_type_id = Call::SOURCE_REDIRECT_CALL;
                        return $this->createHoldCall($callModel, $user);
                    }

                    $response['error'] = 'Not found "user" for Call';
                    $response['error_code'] = 14;

                }

            }

            return $this->createExceptionCall($incoming_phone_number); //$ciscoPhoneNumber

        }

        $response['error'] = 'Not found "call" data';
        $response['error_code'] = 12;
        return $response;
    }


    /**
     * @param array $post
     * @return array
     */
    private function voiceRecord(array $post = []): array
    {
        $response = [];

        $callData = $post['callData'] ?? [];

        //Yii::info(VarDumper::dumpAsString($post), 'info\API:Communication:voiceRecord');

        if ($callData && isset($callData['CallSid'], $callData['RecordingSid']) ) {

            //$call = Call::find()->where(['c_call_sid' => $callData['CallSid']])->one();

//            $call = null;
//            $is_call_incoming = (isset($post['call'],$post['call']['c_call_type_id']) && (int)$post['call']['c_call_type_id'] === Call::CALL_TYPE_IN);
//            if($is_call_incoming) {
//                $call = Call::find()->where(['c_call_sid' => $callData['CallSid']])
//                    //->andWhere(['c_call_status' => Call::CALL_STATUS_COMPLETED])
//                    ->andWhere([ '>', 'c_created_user_id', 0])
//                    ->orderBy(['c_updated_dt' => SORT_DESC])->limit(1)->one();
//            }
//
//            if(!$call) {
//                $call = Call::find()->where(['c_call_sid' => $callData['CallSid']])->orderBy(['c_id' => SORT_ASC])->limit(1)->one();
//            }

            $call = Call::find()->where(['c_recording_sid' => $callData['RecordingSid']])->limit(1)->one();

            if (!$call) {
                $call = Call::find()->where(['c_call_sid' => $callData['CallSid']])->orderBy(['c_id' => SORT_ASC])->limit(1)->one();
            }

            if ($call && $callData['RecordingUrl']) {

                $call->c_recording_url = $callData['RecordingUrl'] ?? null;
                $call->c_recording_duration = $callData['RecordingDuration'] ?? null;

                if(!$call->save()) {
                    Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceRecord:Call:save');
                }

                if ($call->c_lead_id) {
                    //if ($call->c_created_user_id) {
                        // Notifications::create($call->c_created_user_id, 'Call Recording Completed  from ' . $call->c_from . ' to ' . $call->c_to . ' <br>Lead ID: ' . $call->c_lead_id , Notifications::TYPE_INFO, true);
                    //}
                    Notifications::socket(null, $call->c_lead_id, 'recordingUpdate', ['url' => $call->c_recording_url], true);
                }
            }
        } else {
            $response['error'] = 'Not found callData[CallSid] or callData[RecordingSid] in voiceRecord';
        }

        return $response;
    }


    /**
     * @param array $post
     * @return array
     */
    private function voiceFinish(array $post = []): array
    {
        $response = [];
        // Yii::info(VarDumper::dumpAsString($post), 'info\API:Communication:voiceFinish');

        $callData = $post['callData'] ?? [];

        if (isset($callData['sid']) && $callData['sid']) {
            //$call = $this->findOrCreateCallByData($callData);

            $call = Call::find()->where(['c_call_sid' => $callData['sid']])->orderBy(['c_id' => SORT_ASC])->limit(1)->one();

            if ($call) {

                if (isset($callData['price']) && $callData['price']) {
                    $call->c_price = abs((float) $callData['price']);
                }

                if (isset($callData['duration']) && $callData['duration']) {
                    $call->c_call_duration = (int) $callData['duration'];
                }

                if(!$call->save()) {
                    Yii::error(VarDumper::dumpAsString($call->errors), 'API:CommunicationController:actionVoice:TYPE_VOIP_FINISH:Call:save');
                }

            } else {
                Yii::error('Communication Request: Not found Call SID: ' . $callData['sid'], 'API:Communication:voiceFinish:Call:find');
            }
        } else {
            Yii::error('Communication Request: Not found post[callData][sid] ' . VarDumper::dumpAsString($post), 'API:Communication:voiceFinish:post');
        }

        return $response;
    }


    /**
     * @param array $post
     * @return array
     */
    private function voiceClient(array $post = []): array
    {
        $response = [];

        Yii::info(VarDumper::dumpAsString($post), 'info\API:Communication:voiceClient');

        $callSid = $post['callData']['sid'] ?? $post['callData']['CallSid'] ?? null;

        if ($callSid) {

            $call = Call::find()->where(['c_call_sid' => $callSid])->orderBy(['c_id' => SORT_ASC])->limit(1)->one();

            $callData = $post['call'];
            $callOriginalData = $post['callData'] ?? [];

            if(!$call) {
                $call = new Call();
                $call->c_call_sid = $callSid;
                $call->c_call_type_id = (int) $callData['c_call_type_id'];

                if (isset($callOriginalData['ParentCallSid'])) {
                    $call->c_parent_call_sid = $callOriginalData['ParentCallSid'];
                }

                $call->c_from = $callOriginalData['From'] ?? null;
                $call->c_to = $callOriginalData['To'] ?? null;
                $call->c_caller_name = $callOriginalData['Caller'] ?? null;
                $agentId = (int) str_replace('client:seller', '', $call->c_from);

                if(isset($callData['c_project_id']) && $callData['c_project_id']) {
                    $call->c_project_id = (int) $callData['c_project_id'];
                }

                $upp = null;

                if ($call->isOut()) {
                    if (!$call->c_client_id && $call->c_to) {
                        $clientPhone = ClientPhone::find()->where(['phone' => $call->c_to])->orderBy(['id' => SORT_DESC])->limit(1)->one();
                        if ($clientPhone && $clientPhone->client_id) {
                            $call->c_client_id = $clientPhone->client_id;
                        }
                    }

                    if (!$call->c_dep_id && $call->c_project_id && isset($callOriginalData['FromAgentPhone']) && $callOriginalData['FromAgentPhone']) {
                        $upp = UserProjectParams::find()->where(['upp_tw_phone_number' => $callOriginalData['FromAgentPhone'], 'upp_project_id' => $call->c_project_id])->limit(1)->one();
                        if ($upp && $upp->upp_dep_id) {
                            $call->c_dep_id = $upp->upp_dep_id;
                        }
                    }
                }

                if (!$upp && $call->c_project_id && $agentId) {
                    $upp = UserProjectParams::find()->where(['upp_user_id' => $agentId, 'upp_project_id' => $call->c_project_id])->limit(1)->one();
                }

                if (!$upp) {
                    $upp = UserProjectParams::find()->where(['upp_tw_phone_number' => $call->c_from])->one();
                }

                if ($upp && $upp->uppUser) {
                    $call->c_created_user_id = $upp->uppUser->id;
                    $call->c_project_id = $upp->upp_project_id;

                    if (!$call->c_dep_id) {
                        $call->c_dep_id = $upp->upp_dep_id;
                    }
                }
                // Yii::warning('Not found Call: ' . $callSid, 'API:Communication:voiceClient:Call::find');
            }

            if (isset($callOriginalData['lead_id']) && $callOriginalData['lead_id']) {
                $call->c_lead_id = (int) $callOriginalData['lead_id'];
            }

            if (isset($callOriginalData['case_id']) && $callOriginalData['case_id']) {
                $call->c_case_id = (int) $callOriginalData['case_id'];
            }


            if(isset($callOriginalData['CallStatus']) && $callOriginalData['CallStatus']) {
                $call->c_call_status = $callOriginalData['CallStatus'];
                $call->setStatusByTwilioStatus($call->c_call_status);
            }

            if (!$call->c_call_status) {
                Yii::warning('Not found status Call: ' . $callSid . ', ' . VarDumper::dumpAsString($callOriginalData), 'API:Communication:voiceClient:Call::status');
            }


//            if ($call->c_parent_call_sid) {
//
//                $parentCall = Call::find()->where(['c_call_sid' => $call->c_parent_call_sid])->limit(1)->one();
//
//                //Yii::info('voiceClient - Find parent call - ' . $call->c_parent_call_sid . ', sid: ' . $call->c_call_sid, 'info\API:Communication:voiceClient:ParentCallSid');
//
//                if ($parentCall) {
//                    $call->c_parent_id = $parentCall->c_id;
//                    $call->c_project_id = $parentCall->c_project_id;
//
//                    if (!$call->c_dep_id) {
//                        $call->c_dep_id = $parentCall->c_dep_id;
//                    }
//
//                    $call->c_source_type_id = $parentCall->c_source_type_id;
//
//                    if (!$call->c_lead_id) {
//                        $call->c_lead_id = $parentCall->c_lead_id;
//                    }
//
//                    if (!$call->c_case_id) {
//                        $call->c_case_id = $parentCall->c_case_id;
//                    }
//
//                    if ($parentCall->callUserGroups && !$call->callUserGroups) {
//                        foreach ($parentCall->callUserGroups as $cugItem) {
//                            $cug = new CallUserGroup();
//                            $cug->cug_ug_id = $cugItem->cug_ug_id;
//                            $cug->cug_c_id = $call->c_id;
//                            if (!$cug->save()) {
//                                \Yii::error(VarDumper::dumpAsString($cug->errors),
//                                    'API:CommunicationController:findOrCreateCall:CallUserGroup:save');
//                            }
//                        }
//                    }
//                    //$call->c_u_id = $parentCall->c_dep_id;
//                }
//            }

            if($call->c_lead_id && $lead = $call->cLead) {
                if ($lead->isPending() && $lead->isCallProcessing()) {

                    $delayTimeMin = $lead->getDelayPendingTime();
                    $lead->l_pending_delay_dt = date('Y-m-d H:i:s', strtotime('+' . $delayTimeMin . ' minutes'));
                    $lead->employee_id = null;
                    $lead->callReady();

                    if (!$lead->save()) {
                        Yii::error('lead: ' . $lead->id . ' ' . VarDumper::dumpAsString($lead->errors), 'API:Communication:voiceClient:Lead:save');
                    }
                }

                if ($lead->isProcessing() && !$lead->isCallDone()) {
                    $lead->callDone();
                    if (!$lead->save()) {
                        Yii::error('lead: ' . $lead->id . ' ' . VarDumper::dumpAsString($lead->errors), 'API:Communication:voiceClient:Lead:save2');
                    }
                }
            }
            if(!$call->save()) {
                Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceClient:Call:save');
            }
        }
        else {
            Yii::error('Communication Request: Not found post[callData][sid] / post[callData][CallSid] ' . VarDumper::dumpAsString($post), 'API:Communication:voiceClient:post');
        }

        return $response;
    }


    /**
     * @param array $post
     * @return array
     * @throws \Exception
     */
    private function voiceDefault(array $post = []): array
    {

        $response = [];
        $trace = [];

        //Yii::info(VarDumper::dumpAsString($post), 'info\API:Communication:voiceDefault');

        //$agentId = null;

        if (isset($post['callData']['CallSid']) && $post['callData']['CallSid']) {

            $callData = $post['callData'];
            $call = $this->findOrCreateCallByData($callData);

            if($call->isStatusNoAnswer() || $call->isStatusBusy() || $call->isStatusCanceled() || $call->isStatusFailed()) {

                if ($call->c_lead_id) {
                    $lead = $call->cLead2;
                    if ($lead && (int) $lead->l_call_status_id !== Lead::CALL_STATUS_CANCEL) {
                        $lead->l_call_status_id = Lead::CALL_STATUS_CANCEL;
                        if (!$lead->save()) {
                            Yii::error('lead: ' . $lead->id . ' ' . VarDumper::dumpAsString($lead->errors),
                                'API:Communication:voiceDefault:Lead:save');
                        }
                    }
                }

            } /*else {
                $call->c_call_status = $call_status;
                $call->setStatusByTwilioStatus($call_status);
            }*/
            //if(!$childCall) {


            //}

            /*if (!$call->c_price && isset($post['call']['c_tw_price']) && $post['call']['c_tw_price']) {
                $call->c_price = abs((float) $post['call']['c_tw_price']);
            }*/

            /*if(isset($post['call']['c_call_duration']) && $post['call']['c_call_duration']) {
                $call->c_call_duration = (int) $post['call']['c_call_duration'];
            } else {
                $call->c_call_duration = 1;
            }*/

            if(!$call->save()) {
                Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceDefault:Call2:save');
            }

        } else {
            Yii::error('Not found POST[callData][CallSid] ' . VarDumper::dumpAsString($post), 'API:Communication:voiceDefault:callData:notFound');
            $response['error'] = 'Not found POST[callData][CallSid]';
        }

        $response['trace'] = $trace;

        return $response;
    }


    /**
     * @param string $callSid
     * @param string|null $parentCallSid
     * @param array $calData
     * @param int $call_project_id
     * @param int|null $call_dep_id
     * @return Call
     * @throws \Exception
     */
    protected function findOrCreateCall(string $callSid, ?string $parentCallSid, array $calData, int $call_project_id, ?int $call_dep_id): Call
    {
        $call = null;
        $parentCall = null;
        $clientPhone = null;
        
        //error_log("Call Data: " . print_r($calData, true));

        if (isset($calData['From']) && $calData['From']) {
            $clientPhoneNumber = $calData['From'];
            if ($clientPhoneNumber) {
                $clientPhone = ClientPhone::find()->where(['phone' => $clientPhoneNumber])->orderBy(['id' => SORT_DESC])->limit(1)->one();
            }
        }

        if ($callSid) {
            $call = Call::find()->where(['c_call_sid' => $callSid])->limit(1)->one();
        }

        if ($parentCallSid) {
            $parentCall = Call::find()->where(['c_call_sid' => $parentCallSid])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();
        }


        if (!$call) {

            $call = new Call();
            $call->c_call_sid = $calData['CallSid'] ?? null;
            $call->c_call_type_id = Call::CALL_TYPE_IN;
            // $call->c_call_status = Call::TW_STATUS_IVR; //$calData['CallStatus'] ?? Call::CALL_STATUS_QUEUE;
            $call->setStatusIvr();

            $call->c_com_call_id = $calData['c_com_call_id'] ?? null;
            $call->c_parent_call_sid = $calData['ParentCallSid'] ?? null;
            $call->c_offset_gmt = Call::getClientTime($calData);
            $call->c_from_country = Call::getDisplayRegion($calData['FromCountry'] ?? '');
            $call->c_from_state = $calData['FromState'] ?? null;
            $call->c_from_city = $calData['FromCity'] ?? null;

            if ($parentCall) {
                $call->c_parent_id = $parentCall->c_id;
                $call->c_project_id = $parentCall->c_project_id;
                $call->c_dep_id = $parentCall->c_dep_id;
                $call->c_source_type_id = $parentCall->c_source_type_id;

                if ($parentCall->callUserGroups && !$call->callUserGroups) {
                    foreach ($parentCall->callUserGroups as $cugItem) {
                        $cug = new CallUserGroup();
                        $cug->cug_ug_id = $cugItem->cug_ug_id;
                        $cug->cug_c_id = $call->c_id;
                        if (!$cug->save()) {
                            \Yii::error(VarDumper::dumpAsString($cug->errors), 'API:CommunicationController:findOrCreateCall:CallUserGroup:save');
                        }
                    }
                }
                    //$call->c_u_id = $parentCall->c_dep_id;
            }

            if ($call_project_id) {
                $call->c_project_id = $call_project_id;
            }
            if ($call_dep_id) {
                $call->c_dep_id = $call_dep_id;
            }

            $call->c_is_new = true;
            $call->c_created_dt = date('Y-m-d H:i:s');
            $call->c_from = $calData['From'];
            $call->c_to = $calData['To']; //Called
            $call->c_created_user_id = null;

            if ($clientPhone && $clientPhone->client_id) {
                $call->c_client_id = $clientPhone->client_id;
            }

//            if ($call->c_dep_id === Department::DEPARTMENT_SALES) {
//                /*$lead = Lead2::findLastLeadByClientPhone($call->c_from, $call->c_project_id);
//                if ($lead) {
//                    $call->c_lead_id = $lead->id;
//                }*////
//            } elseif ($call->c_dep_id === Department::DEPARTMENT_EXCHANGE || $call->c_dep_id === Department::DEPARTMENT_SUPPORT) {
//
//            }

            if (!$call->save()) {
                \Yii::error(VarDumper::dumpAsString($call->errors), 'API:CommunicationController:findOrCreateCall:Call:save');
                throw new \Exception('findOrCreateCall: Can not save call in db', 1);
            }
        }

        return $call;
    }


    /**
     * @param array $callData
     * @return Call
     * @throws \Exception
     */
    protected function findOrCreateCallByData(array $callData): Call
    {
        $call = null;
        $parentCall = null;
        // $clientPhone = null;

//        if (isset($callData['From']) && $callData['From']) {
//            $clientPhoneNumber = $callData['From'];
//            if ($clientPhoneNumber) {
//                $clientPhone = ClientPhone::find()->where(['phone' => $clientPhoneNumber])->orderBy(['id' => SORT_DESC])->limit(1)->one();
//            }
//        }

        $callSid = $callData['CallSid'] ?? '';
        $parentCallSid = $callData['ParentCallSid'] ?? '';

        if ($callSid) {
            $call = Call::find()->where(['c_call_sid' => $callSid])->limit(1)->one();
        }

        if ($parentCallSid) {
            $parentCall = Call::find()->where(['c_call_sid' => $parentCallSid])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();
        }


        if (!$call) {

            $call = new Call();
            $call->c_call_sid = $callData['CallSid'] ?? null;
            $call->c_parent_call_sid = $callData['ParentCallSid'] ?? null;
            $call->c_com_call_id = $callData['c_com_call_id'] ?? null;
            $call->c_call_type_id = Call::CALL_TYPE_IN;


            if ($parentCall) {
                $call->c_parent_id = $parentCall->c_id;
                $call->c_project_id = $parentCall->c_project_id;
                $call->c_dep_id = $parentCall->c_dep_id;
                $call->c_source_type_id = $parentCall->c_source_type_id;


                $call->c_lead_id = $parentCall->c_lead_id;
                $call->c_case_id = $parentCall->c_case_id;
                $call->c_client_id = $parentCall->c_client_id;

                $call->c_created_user_id = $parentCall->c_created_user_id;

                $call->c_call_type_id = $parentCall->c_call_type_id;

                /*if ($parentCall->c_lead_id) {

                }*/

                if ($parentCall->callUserGroups && !$call->callUserGroups) {
                    foreach ($parentCall->callUserGroups as $cugItem) {
                        $cug = new CallUserGroup();
                        $cug->cug_ug_id = $cugItem->cug_ug_id;
                        $cug->cug_c_id = $call->c_id;
                        if (!$cug->save()) {
                            \Yii::error(VarDumper::dumpAsString($cug->errors), 'API:CommunicationController:findOrCreateCall:CallUserGroup:save');
                        }
                    }
                }
                //$call->c_u_id = $parentCall->c_dep_id;
            }

//            if ($call_project_id) {
//                $call->c_project_id = $call_project_id;
//            }
//            if ($call_dep_id) {
//                $call->c_dep_id = $call_dep_id;
//            }

            $call->c_is_new = true;
            $call->c_created_dt = date('Y-m-d H:i:s');
            $call->c_from = $callData['From'];
            $call->c_to = $callData['To']; //Called
            $call->c_created_user_id = null;

            /*if ($clientPhone && $clientPhone->client_id) {
                $call->c_client_id = $clientPhone->client_id;
            }*/

//            if ($call->c_dep_id === Department::DEPARTMENT_SALES) {
//                /*$lead = Lead2::findLastLeadByClientPhone($call->c_from, $call->c_project_id);
//                if ($lead) {
//                    $call->c_lead_id = $lead->id;
//                }*////
//            } elseif ($call->c_dep_id === Department::DEPARTMENT_EXCHANGE || $call->c_dep_id === Department::DEPARTMENT_SUPPORT) {
//
//            }

            /*if (!$call->save()) {
                \Yii::error(VarDumper::dumpAsString($call->errors), 'API:CommunicationController:findOrCreateCallByData:Call:save');
                throw new \Exception('findOrCreateCallByData: Can not save call in db', 1);
            }*/
        }



        /*if ($call->isFailed() || $call->isNoAnswer() || $call->isBusy() || $call->isCanceled()) {
            $call->c_call_status = $callData['CallStatus'];

        } else {*/

        $call->c_call_status = $callData['CallStatus'];
        $call->setStatusByTwilioStatus($call->c_call_status);
            //$statusId = $call->setStatusByTwilioStatus($call->c_call_status);
            //$call->c_status_id = $statusId;
        //}


        $agentId = null;

        if (isset($callData['Called']) && $callData['Called']) {
            if(strpos($callData['Called'], 'client:seller') !== false) {
                $agentId = (int) str_replace('client:seller', '', $callData['Called']);
            }
        }

        if (!$agentId) {
            if (isset($callData['c_user_id']) && $callData['c_user_id']) {
                $agentId = (int) $callData['c_user_id'];
            }
        }

        if ($agentId) {
            $call->c_created_user_id = $agentId;
        }

        if (!$call->c_created_user_id && $parentCall && $call->isOut()) {
            $call->c_created_user_id = $parentCall->c_created_user_id;
        }

        if (isset($callData['SequenceNumber'])) {
            $call->c_sequence_number = (int) ( $callData['SequenceNumber'] ?? 0 );
        }

        if (isset($callData['CallDuration'])) {
            $call->c_call_duration = (int) $callData['CallDuration'];
        }

        if (isset($callData['ForwardedFrom']) && $callData['ForwardedFrom']) {
            $call->c_forwarded_from = $callData['ForwardedFrom'];
            // $call->c_source_type_id = Call::SOURCE_TRANSFER_CALL;
        }

        if (!$call->c_recording_sid && isset($callData['RecordingSid']) && $callData['RecordingSid']) {
            $call->c_recording_sid = $callData['RecordingSid'];
        }

        return $call;
    }


    /**
     * @param Call $callModel
     * @param Employee $user
     * @param array $stepParams
     * @return array
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    protected function createDirectCall(Call $callModel, Employee $user): array
    {
        $jobId = null;
        $callModel->c_created_user_id = $user->id;
        $callModel->c_source_type_id = Call::SOURCE_DIRECT_CALL;

        if (!$callModel->update()) {
            Yii::error(VarDumper::dumpAsString($callModel->errors), 'API:Communication:createDirectCall:Call:update');
        } else {
            $job = new CallQueueJob();
            $job->call_id = $callModel->c_id;
            $job->delay = 0;
            $jobId = Yii::$app->queue_job->delay(7)->priority(90)->push($job);
        }

        $project = $callModel->cProject;
//        $url_say_play_hold = '';
//        $url_music_play_hold = 'https://talkdeskapp.s3.amazonaws.com/production/audio_messages/folk_hold_music.mp3';

        $responseTwml = new VoiceResponse();

        if ($project && $project->custom_data) {
            $customData = @json_decode($project->custom_data, true);
            if ($customData) {
//                if(isset($customData['url_say_play_hold']) && $customData['url_say_play_hold']) {
//                    $url_say_play_hold = $customData['url_say_play_hold'];
//                }

                if (isset($customData['play_direct_message'])) {
                    if($customData['play_direct_message']) {
                        $responseTwml->play($customData['play_direct_message']);
                    } else  {
                        if (isset($customData['say_direct_message']) && $customData['say_direct_message']) {
                            $responseTwml->say($customData['say_direct_message'], [
                                'language' => 'en-US',
                                'voice' => 'alice'
                            ]);
                        }
                    }
                }

                if(isset($customData['url_music_play_hold']) && $customData['url_music_play_hold']) {
                    $responseTwml->play($customData['url_music_play_hold'], ['loop' => 0]);
                }

            }
        }

        $callInfo = [];

        $callInfo['id'] = $callModel->c_id;
        $callInfo['project_id'] = $callModel->c_project_id;
        $callInfo['dep_id'] = $callModel->c_dep_id;
        $callInfo['status'] = $callModel->c_call_status;
        $callInfo['status_id'] = $callModel->c_status_id;
        $callInfo['source_type'] = $callModel->c_source_type_id;



        $response = [];
        $response['jobId'] = $jobId;
        $response['call'] = $callInfo;
        $response['twml'] = (string) $responseTwml;
        $responseData = [
            'status' => 200,
            'name' => 'Success',
            'code' => 0,
            'message' => '',
            'data' => ['response' => $response]
        ];
        return $responseData;
    }

    /**
     * @param Call $callModel
     * @param Employee $user
     * @return array
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    protected function createHoldCall(Call $callModel, Employee $user): array
    {

        $callModel->c_created_user_id = null;
        $callModel->c_source_type_id = Call::SOURCE_REDIRECT_CALL;

        if(!$callModel->update()) {
            Yii::error(VarDumper::dumpAsString($callModel->errors), 'API:Communication:createDirectCall:Call:update');
        } else {
            $job = new CallQueueJob();
            $job->call_id = $callModel->c_id;
            $job->delay = 0;
            $jobId = Yii::$app->queue_job->delay(7)->priority(100)->push($job);
        }


        $project = $callModel->cProject;

        //$url_say_play_hold = '';
        //$url_music_play_hold = 'https://talkdeskapp.s3.amazonaws.com/production/audio_messages/folk_hold_music.mp3';

        $responseTwml = new VoiceResponse();

        if($project && $project->custom_data) {
            $customData = @json_decode($project->custom_data, true);
            if($customData) {

//                if(isset($customData['url_say_play_hold']) && $customData['url_say_play_hold']) {
//                    $url_say_play_hold = $customData['url_say_play_hold'];
//                }

                if(isset($customData['play_redirect_message'])) {
                    if($customData['play_redirect_message']) {
                        $responseTwml->play($customData['play_redirect_message']);
                    } else  {
                        if(isset($customData['say_redirect_message']) && $customData['say_redirect_message']) {
                            $responseTwml->say($customData['say_redirect_message'], [
                                'language' => 'en-US',
                                'voice' => 'alice'
                            ]);
                        }
                    }
                }

                if(isset($customData['url_music_play_hold']) && $customData['url_music_play_hold']) {
                    $responseTwml->play($customData['url_music_play_hold'], ['loop' => 0]);
                }

            }
        }


        $response = [];
        $response['twml'] = (string) $responseTwml;
        $responseData = [
            'status' => 200,
            'name' => 'Success',
            'code' => 0,
            'message' => '',
            'data' => ['response' => $response]
        ];
        return $responseData;
    }

    /**
     * @param string $phoneNumber
     * @return array
     */
    protected function createExceptionCall(string $phoneNumber): array
    {
        Yii::error('Number is temporarily not working ('.$phoneNumber.')', 'API:Communication:createExceptionCall');

        $responseTwml = new VoiceResponse();
        $responseTwml->say('Sorry, this number is temporarily not working.', [
                'language' => 'en-US',
                'voice' => 'alice'
            ]);
        $responseTwml->reject(['reason' => 'busy']);

        $response = [];
        $response['twml'] = (string) $responseTwml;
        $responseData = [
            'status' => 200,
            'name' => 'Success',
            'code' => 0,
            'message' => '',
            'data' => ['response' => $response]
        ];
        return $responseData;
    }


    protected function startCallService(Call $callModel, DepartmentPhoneProject $department, int $ivrSelectedDigit, array $stepParams): array
    {

        if(isset(Department::DEPARTMENT_LIST[$ivrSelectedDigit])) {
            $callModel->c_dep_id = $ivrSelectedDigit;
            if(!$callModel->save()) {
                Yii::error(VarDumper::dumpAsString($callModel->errors), 'API:Communication:startCallService:Call:update');
            }

            $job = new CallQueueJob();
            $job->call_id = $callModel->c_id;
            $job->delay = 0;
            $jobId = Yii::$app->queue_job->delay(7)->priority(100)->push($job);
        }

        $choice = $stepParams['digits'][$ivrSelectedDigit] ?? null;
        $responseTwml = new VoiceResponse();

        if(isset($stepParams['before_say']) && $stepParams['before_say']) {
            $responseTwml->say($stepParams['before_say'], ['language' => $stepParams['language'], 'voice' => $stepParams['voice']]);
        }

        if($choice) {
            if(isset($choice['pause']) && $choice['pause']) {
                $responseTwml->pause(['length' => $choice['pause']]);
            }
            if(isset($choice['say'])) {
                $responseTwml->say($choice['say'], ['language' => $choice['language'], 'voice' => $choice['voice']]);
            }

            if(isset($stepParams['after_say']) && $stepParams['after_say']) {
                $responseTwml->say($stepParams['after_say'], ['language' => $stepParams['language'], 'voice' => $stepParams['voice']]);
            }

            $responseTwml->play($choice['play'], ['loop' => 0]);
        }



        $response = [];
        $response['twml'] = (string) $responseTwml;
        $responseData = [
            'status' => 200,
            'name' => 'Success',
            'code' => 0,
            'message' => '',
            'data' => ['response' => $response]
        ];
        return $responseData;
    }




    /**
     * @param Call $callModel
     * @param DepartmentPhoneProject $department
     * @param int $ivrStep
     * @param int|null $ivrSelectedDigit
     * @return array
     */
    protected function ivrService(Call $callModel, DepartmentPhoneProject $department, int $ivrStep, ?int $ivrSelectedDigit): array
    {
        $response = [];


        /*Yii::info(VarDumper::dumpAsString([
            'callModel' => $callModel->attributes,
            'department' => $department->attributes,
            'ivrSelectedDigit' => $ivrSelectedDigit,
            'ivrStep' => $ivrStep,

        ], 10, false), 'info\API:Communication:ivrService');*/


        try {
            //$params_voice_gather = \Yii::$app->params['voice_gather'];

            $dParams = @json_decode($department->dpp_params, true);
            $ivrParams = $dParams['ivr'] ?? [];

            $stepParams = [];

            if(isset($ivrParams['steps'][$ivrStep])) {
                $stepParams = $ivrParams['steps'][$ivrStep];
            }


            $company = '';
            if ($callModel->cProject && $callModel->cProject->name) {
                $company = ' ' . strtolower($callModel->cProject->name);
            }


            if($ivrStep === 2) {

                $ivrSelectedDigit = (int) $ivrSelectedDigit;

                if ($ivrSelectedDigit) {
                    return $this->startCallService($callModel, $department, $ivrSelectedDigit, $stepParams);
                }

                $responseTwml = new VoiceResponse();
                $responseTwml->pause(['length' => 2]);
                //$responseTwml->say('Selected number '.$ivrSelectedDigit . '. Goodbye! ');
                //$responseTwml->reject(['reason' => 'busy']);
                $responseTwml->say($ivrParams['error_phrase'], ['language' => $ivrParams['entry_language'], 'voice' => $ivrParams['entry_voice']]);
                $responseTwml->redirect('/v1/twilio/voice-gather/?step=1', ['method' => 'POST']);


                $response['twml'] = (string) $responseTwml;
                $responseData = [
                    'status' => 200,
                    'name' => 'Success',
                    'code' => 0,
                    'message' => '',
                    'data' => ['response' => $response]
                ];


                return $responseData;
            }

            if ($callModel && !$callModel->isStatusIvr()) {
                // $callModel->c_call_status = Call::TW_STATUS_IVR;
                $callModel->setStatusIvr(); //setStatusByTwilioStatus($callModel->c_call_status);
                $callModel->update();
            }

            $responseTwml = new VoiceResponse();

            if(isset($ivrParams['entry_pause']) && $ivrParams['entry_pause']) {
                $responseTwml->pause(['length' => $ivrParams['entry_pause']]);
            }

            $entry_phrase = isset($ivrParams['entry_phrase']) ? str_replace('{{project}}', $company, $ivrParams['entry_phrase']) : null;

            if($entry_phrase) {
                $responseTwml->say($entry_phrase, ['language' => $ivrParams['entry_language'], 'voice' => $ivrParams['entry_voice']]);
            }


            if(isset($ivrParams['steps'])) {

                $gather = $responseTwml->gather([
                    'action' => '/v1/twilio/voice-gather/?step=2',
                    'method' => 'POST',
                    'numDigits' => 1,
                    'timeout' => 5,
                    //'actionOnEmptyResult' => true,
                ]);


                $stepParams = $ivrParams['steps'][$ivrStep] ?? [];

                if (isset($stepParams['before_say']) && $stepParams['before_say']) {
                    $gather->say($ivrParams['steps'][$ivrStep]['before_say'], ['language' => $stepParams['language'], 'voice' => $stepParams['voice']]);
                }

                $after_say = '';
                if (isset($stepParams['after_say']) && $stepParams['after_say']) {
                    $after_say = $stepParams['after_say'];
                }

                if (isset($stepParams['choice']) && $stepParams['choice']) {
                    foreach ($stepParams['choice'] as $sayItem) {
                        $gather->say($sayItem['say'] . ' ' . $after_say, ['language' => $stepParams['language'], 'voice' => $stepParams['voice']]);
                        if (isset($sayItem['pause']) && $sayItem['pause']) {
                            $gather->pause(['length' => $sayItem['pause']]);
                        }
                    }
                }

                if (isset($stepParams['after_say']) && $stepParams['after_say']) {
                    $gather->say($stepParams['after_say'], ['language' => $stepParams['language'], 'voice' => $stepParams['voice']]);
                }


                $responseTwml->say($ivrParams['error_phrase']);
                $responseTwml->redirect('/v1/twilio/voice-gather/?step=1', ['method' => 'POST']);
            } else {


                if(isset(Department::DEPARTMENT_LIST[$department->dpp_dep_id])) {
                    $callModel->c_dep_id = $department->dpp_dep_id;
                    if(!$callModel->save()) {
                        Yii::error(VarDumper::dumpAsString($callModel->errors), 'API:Communication:startCallService:Call:update2');
                    }

                    $job = new CallQueueJob();
                    $job->call_id = $callModel->c_id;
                    $job->delay = 0;
                    $jobId = Yii::$app->queue_job->delay(7)->priority(80)->push($job);
                }

                if(isset($ivrParams['hold_play']) && $ivrParams['hold_play']) {
                    $responseTwml->play($ivrParams['hold_play'], ['loop' => 0]);
                }

            }

            $response['twml'] = (string) $responseTwml;
            $responseData = [
                'status' => 200,
                'name' => 'Success',
                'code' => 0,
                'message' => ''
            ];
            $responseData['data']['response'] = $response;

        } catch (\Throwable $e) {
            $responseTwml = new VoiceResponse();
            $responseTwml->reject(['reason' => 'busy']);
            $response['twml'] = (string) $responseTwml;
            $responseData = [
                'status' => 404,
                'name' => 'Error',
                'code' => 404,
                'message' => 'Sales Communication error: '. $e->getMessage(). "\n" . $e->getFile() . ':' . $e->getLine(),
            ];
            $responseData['data']['response'] = $response;
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

        /*
         * [
                'sq_id' => '257'
                'sq_status_id' => '5'
                'sq_project_id' => '6'
                'sq_num_segments' => '2'
                'sms' => [
                    'sq_id' => '257'
                    'sq_project_id' => '6'
                    'sq_phone_from' => '+15596489977'
                    'sq_phone_to' => '+37360368365'
                    'sq_sms_text' => 'WOWFARE best price (per adult) to London:'
                    'sq_sms_data' => '{\"project_id\":\"6\"}'
                    'sq_type_id' => '2'
                    'sq_language_id' => 'en-US'
                    'sq_job_id' => '9058'
                    'sq_priority' => '2'
                    'sq_status_id' => '5'
                    'sq_delay' => '0'
                    'sq_status_done_dt' => '2019-02-08 09:25:16'
                    'sq_tw_message_id' => 'SM591824e067f7459e9da3134dd8fe5b77'
                    'sq_tw_num_segments' => '2'
                    'sq_tw_status' => 'queued'
                    'sq_tw_uri' => '/2010-04-01/Accounts/AC10f3c74efba7b492cbd7dca86077736c/Messages/SM591824e067f7459e9da3134dd8fe5b77.json'
                    'sq_created_api_user_id' => '8'
                    'sq_created_dt' => '2019-02-08 09:25:15'
                    'sq_updated_dt' => '2019-02-08 09:25:16'
                ]
                'action' => 'update'
                'type' => 'update_sms_status'
            ]
         */


        $sq_id = (int) Yii::$app->request->post('sq_id');
        $sq_status_id = (int) Yii::$app->request->post('sq_status_id');

        //$sq_price = Yii::$app->request->post('sq_price');

        $smsParams = Yii::$app->request->post('sms');

        // $sq_project_id = Yii::$app->request->post('sq_project_id');

        try {

            // Yii::info(VarDumper::dumpAsString(Yii::$app->request->post()), 'info\updateSmsStatus');

            if(!$sq_id) {
                throw new NotFoundHttpException('Not found sq_id', 11);
            }

            if(!$sq_status_id) {
                throw new NotFoundHttpException('Not found sq_status_id', 12);
            }

            $sid =  $smsParams['sq_tw_message_id'] ?? null;

            $sms = null;

            if($sid) {
                $sms = Sms::findOne(['s_tw_message_sid' => $sid]);
            }

            if(!$sms) {
                $sms = Sms::findOne(['s_communication_id' => $sq_id]);
            }


            if($sms) {

                if($sq_status_id > 0) {
                    $sms->s_status_id = $sq_status_id;
                    if($sq_status_id === Sms::STATUS_DONE) {
                        $sms->s_status_done_dt = date('Y-m-d H:i:s');
                    }

                    if($smsParams) {
                        if(isset($smsParams['sq_tw_price']) && $smsParams['sq_tw_price']) {
                            $sms->s_tw_price = abs((float) $smsParams['sq_tw_price']);
                        }

                        if(isset($smsParams['sq_tw_num_segments']) && $smsParams['sq_tw_num_segments']) {
                            $sms->s_tw_num_segments = (int) $smsParams['sq_tw_num_segments'];
                        }

                        if(isset($smsParams['sq_tw_status']) && $smsParams['sq_tw_status']) {
                            $sms->s_error_message = 'status: ' .  $smsParams['sq_tw_status'];
                        }

                        if(!$sms->s_tw_message_sid && isset($smsParams['sq_tw_message_id']) && $smsParams['sq_tw_message_id']) {
                            $sms->s_tw_message_sid = $smsParams['sq_tw_message_id'];
                        }

                    }

                    if(!$sms->save()) {
                        Yii::error(VarDumper::dumpAsString($sms->errors), 'API:Communication:updateSmsStatus:Sms:save');
                    }
                }

                $response['sms'] = $sms->s_id;
            } else {
                $response['error'] = 'Not found SMS ID ('.$sq_id.')';
                $response['error_code'] = 13;
            }


        } catch (\Throwable $e) {
            Yii::error($e->getTraceAsString(), 'API:Communication:updateSmsStatus:try');
            $message = $this->debug ? $e->getTraceAsString() : $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
            $response['error'] = $message;
            $response['error_code'] = 15;
        }

        return $response;
    }



    private function smsFinish(): array
    {

        /*
         * account_sid: "AC10f3c74efba7b492cbd7dca86077736c"
            api_version: "2010-04-01"
            body: "WOWFARE best price (per adult) to Kathmandu:
            ↵$ 1905.05 (s short layovers), https://wowfare.com/q/5c5b5180c6d29
            ↵Regards, Nancy"
            date_created: "Wed, 06 Feb 2019 21:30:12 +0000"
            date_sent: "Wed, 06 Feb 2019 21:30:12 +0000"
            date_updated: "Wed, 06 Feb 2019 21:30:12 +0000"
            direction: "outbound-api"
            error_code: null
            error_message: null
            from: "+16692011645"
            messaging_service_sid: null
            num_media: "0"
            num_segments: "2"
            price: "-0.01500"
            price_unit: "USD"
            sid: "SMb40bfd6908184ec0a51e20789979e304"
            status: "delivered"
            subresource_uris: {,…}
            media: "/2010-04-01/Accounts/AC10f3c74efba7b492cbd7dca86077736c/Messages/SMb40bfd6908184ec0a51e20789979e304/Media.json"
            to: "+15122036074"
            uri: "/2010-04-01/Accounts/AC10f3c74efba7b492cbd7dca86077736c/Messages/SMb40bfd6908184ec0a51e20789979e304.json"
         */

        $response = [];

        try {

            $smsData = Yii::$app->request->post('smsData');
            $comId = Yii::$app->request->post('sq_id');

            if(!$smsData) {
                throw new NotFoundHttpException('Not found smsData', 11);
            }

            if(!$smsData['sid']) {
                throw new NotFoundHttpException('Not found smsData[sid]', 12);
            }



            $sms = Sms::findOne(['s_tw_message_sid' => $smsData['sid']]);

            if(!$sms) {
                $sms = Sms::findOne(['s_communication_id' => $comId]);
            }


            if($sms) {

                if(isset($smsData['price'])) {
                    $sms->s_tw_price = abs((float) $smsData['price']);
                }

                if(isset($smsData['num_segments']) && $smsData['num_segments']) {
                    $sms->s_tw_num_segments = (int) $smsData['num_segments'];
                }

                if(isset($smsData['sid']) && $smsData['sid']) {
                    if(!$sms->s_tw_message_sid) {
                        $sms->s_tw_message_sid = $smsData['sid'];
                    }
                }

                if(isset($smsData['account_sid']) && $smsData['account_sid']) {
                    if(!$sms->s_tw_account_sid) {
                        $sms->s_tw_account_sid = $smsData['account_sid'];
                    }
                }


                if(isset($smsData['status'])) {

                    $sms->s_error_message = 'status: ' . $smsData['status'];

                    if($smsData['status'] === 'delivered') {
                        $sms->s_status_id = SMS::STATUS_DONE;
                    }
                }

                if(!$sms->save()) {
                    Yii::error(VarDumper::dumpAsString($sms->errors), 'API:Communication:smsFinish:Sms:save');
                }
                $response['sms'] = $sms->attributes;

            } else {
                $response['error'] = 'Not found SMS message_sid ('.$smsData['sid'].') and not found CommId ('.$comId.')';
                $response['error_code'] = 13;
            }


        } catch (\Throwable $e) {
            Yii::error($e->getTraceAsString(), 'API:Communication:smsFinish:try');
            $message = $this->debug ? $e->getTraceAsString() : $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
            $response['error'] = $message;
            $response['error_code'] = $e->getCode();
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

                $lastEmail = Email::find()->where(['>', 'e_inbox_email_id', 0])->orderBy(['e_inbox_email_id' => SORT_DESC])->limit(1)->one();

                if ($lastEmail) {
                    //$filter['last_dt'] = $lastEmail->e_inbox_created_dt;
                    $filter['last_id'] = $lastEmail->e_inbox_email_id + 1;
                } else {
                    $filter['last_id'] = 18100;
                }
            } else {
                $filter['last_id'] = (int)$last_id;

                $checkLastEmail = Email::find()->where(['e_inbox_email_id' => $filter['last_id']])->limit(1)->one();
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

            //Yii::info('JOB (' .VarDumper::dumpAsString($response).') Push ' . VarDumper::dumpAsString($data) . ' last_id: ' . $last_id, 'info\API:newEmailMessagesReceived');

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

                    $sms = new Sms();
                    $sms->s_type_id = Sms::TYPE_INBOX;
                    $sms->s_status_id = Sms::STATUS_DONE;
                    $sms->s_is_new = true;

                    $sms->s_status_done_dt = isset($smsItem['si_sent_dt']) ? date('Y-m-d H:i:s', strtotime($smsItem['si_sent_dt'])) : null;

                    //$sms->s_communication_id = $smsItem['si_id'] ?? null;

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

    /**
     * @param array $response
     * @param ApiLog $apiLog
     * @return array
     * @throws UnprocessableEntityHttpException
     */
    private function getResponseData(array $response, ApiLog $apiLog): array
    {
        if (isset($response['error']) && $response['error']) {
            $responseData = [];
        } else {
            $responseData = [
                'status'    => 200,
                'name'      => 'Success',
                'code'      => 0,
                'message'   => ''
            ];
        }

        $responseData['data']['response'] = $response;
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
}
