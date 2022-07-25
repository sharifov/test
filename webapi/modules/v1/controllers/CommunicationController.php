<?php

namespace webapi\modules\v1\controllers;

use common\components\antispam\CallAntiSpamDto;
use common\components\jobs\CallQueueJob;
use common\components\purifier\Purifier;
use common\helpers\LogHelper;
use common\models\ApiLog;
use common\models\Call;
use common\models\CallUserGroup;
use common\models\Client;
use common\models\ClientPhone;
use common\models\Conference;
use common\models\ConferenceParticipant;
use common\models\ConferenceRoom;
use common\models\Department;
use common\models\DepartmentEmailProject;
use common\models\DepartmentPhoneProject;
use common\models\Email;
use common\models\Employee;
use common\models\Lead;
use common\models\Notifications;
use common\models\Sms;
use common\models\Sources;
use common\models\UserProjectParams;
use DomainException;
use frontend\helpers\JsonHelper;
use frontend\widgets\newWebPhone\call\socket\RemoveIncomingRequestMessage;
use frontend\widgets\newWebPhone\sms\socket\Message;
use frontend\widgets\notification\NotificationMessage;
use src\entities\cases\Cases;
use src\forms\lead\PhoneCreateForm;
use src\guards\call\CallRedialGuard;
use src\helpers\app\AppHelper;
use src\helpers\DuplicateExceptionChecker;
use src\helpers\LogExecutionTime;
use src\helpers\setting\SettingHelper;
use src\model\call\exceptions\CallFinishedException;
use src\model\call\exceptions\UniqueCallNotFoundException;
use src\model\call\form\CallCustomParameters;
use src\model\call\services\QueueLongTimeNotificationJobCreator;
use src\model\call\services\RepeatMessageCallJobCreator;
use src\model\callLog\services\CallLogConferenceTransferService;
use src\model\callLog\services\CallLogTransferService;
use src\model\callLogFilterGuard\entity\CallLogFilterGuard;
use src\model\callLogFilterGuard\entity\CallLogFilterGuardScopes;
use src\model\callLogFilterGuard\repository\CallLogFilterGuardRepository;
use src\model\callLogFilterGuard\service\CallLogFilterGuardService;
use src\model\callTerminateLog\service\CallTerminateLogService;
use src\model\conference\useCase\recordingStatusCallBackEvent\ConferenceRecordingStatusCallbackForm;
use src\model\conference\useCase\statusCallBackEvent\ConferenceStatusCallbackForm;
use src\model\conference\useCase\statusCallBackEvent\ConferenceStatusCallbackHandler;
use src\model\contactPhoneList\service\ContactPhoneListService;
use src\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfo;
use src\model\contactPhoneServiceInfo\service\ContactPhoneInfoService;
use src\model\department\departmentPhoneProject\entity\params\QueueLongTimeNotificationParams;
use src\model\emailList\entity\EmailList;
use src\model\leadRedial\assign\LeadRedialUnAssigner;
use src\model\leadRedial\queue\AutoTakeJob;
use src\model\phoneList\entity\PhoneList;
use src\model\phoneList\services\InternalPhones;
use src\model\sms\entity\smsDistributionList\SmsDistributionList;
use src\model\user\entity\userStatus\UserStatus;
use src\model\userVoiceMail\entity\UserVoiceMail;
use src\model\voiceMailRecord\entity\VoiceMailRecord;
use src\model\voip\phoneDevice\device\VoipDevice;
use src\repositories\client\ClientsQuery;
use src\repositories\lead\LeadRepository;
use src\repositories\user\UserProjectParamsRepository;
use src\services\call\CallDeclinedException;
use src\services\call\CallFromInternalNumberException;
use src\services\call\CallService;
use src\services\cases\CasesCommunicationService;
use src\services\client\ClientCreateForm;
use src\services\client\ClientManageService;
use src\services\departmentPhoneProject\DepartmentPhoneProjectParamsService;
use src\services\phone\blackList\PhoneBlackListManageService;
use src\services\phone\callFilterGuard\TwilioCallFilterGuard;
use src\services\phone\callFilterGuard\CallFilterGuardService;
use src\services\phone\checkPhone\CheckPhoneService;
use src\services\sms\incoming\SmsIncomingForm;
use src\services\sms\incoming\SmsIncomingService;
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
use src\repositories\email\EmailRepositoryFactory;

/**
 * Class CommunicationController
 *
 * @property CallService $callService
 * @property ConferenceStatusCallbackHandler $conferenceStatusCallbackHandler
 */
class CommunicationController extends ApiBaseController
{
    public const TYPE_VOIP_RECORD       = 'voip_record';
    public const TYPE_VOIP_INCOMING     = 'voip_incoming';
    public const TYPE_VOIP_GATHER       = 'voip_gather';
    public const TYPE_VOIP_CLIENT       = 'voip_client';
    public const TYPE_VOIP_FINISH       = 'voip_finish';
    public const TYPE_VOIP_JOIN_TO_WRONG_CONFERENCE = 'voip_join_to_wrong_conference';

    public const TYPE_VOIP_CONFERENCE   = 'voip_conference';
    public const TYPE_VOIP_CONFERENCE_RECORD   = 'voip_conference_record';

    public const TYPE_VOIP_CONFERENCE_CALL   = 'voip_conference_call';
    public const TYPE_VOIP_CONFERENCE_CALL_RECORD   = 'voip_conference_call_record';


    public const TYPE_UPDATE_EMAIL_STATUS = 'update_email_status';
    public const TYPE_UPDATE_SMS_STATUS = 'update_sms_status';

    public const TYPE_NEW_EMAIL_MESSAGES_RECEIVED = 'new_email_messages_received';
    public const TYPE_NEW_SMS_MESSAGES_RECEIVED = 'new_sms_messages_received';

    public const TYPE_SMS_FINISH        = 'sms_finish';

    private $callService;
    private $conferenceStatusCallbackHandler;

    /**
     * @param $id
     * @param $module
     * @param CallService $callService
     * @param array $config
     */
    public function __construct($id, $module, CallService $callService, ConferenceStatusCallbackHandler $conferenceStatusCallbackHandler, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->callService = $callService;
        $this->conferenceStatusCallbackHandler = $conferenceStatusCallbackHandler;
    }

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
//        $this->startApiLog($this->action->uniqueId);

        $type = Yii::$app->request->post('type');
        $last_id = Yii::$app->request->post('last_email_id', null);

        if (!$type) {
            throw new NotFoundHttpException('Not found Email type', 1);
        }

        switch ($type) {
            case self::TYPE_UPDATE_EMAIL_STATUS:
                $response = $this->updateEmailStatus();
                break;
            case self::TYPE_NEW_EMAIL_MESSAGES_RECEIVED:
                $response = $this->newEmailMessagesReceived($last_id);
                break;
            default:
                throw new BadRequestHttpException('Invalid Email type', 2);
        }

        return $this->getResponseData($response, false);
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
        $this->startApiLog($this->action->uniqueId);
        $type = Yii::$app->request->post('type');

        if (!$type) {
            throw new NotFoundHttpException('Not found type', 1);
        }

        switch ($type) {
            case self::TYPE_UPDATE_SMS_STATUS:
                $response = $this->updateSmsStatus();
                break;
            case self::TYPE_NEW_SMS_MESSAGES_RECEIVED:
                $response = $this->newSmsMessagesReceived();
                break;
            case self::TYPE_SMS_FINISH:
                $response = $this->smsFinish();
                break;
            default:
                throw new BadRequestHttpException('Invalid type', 2);
        }

        return $this->getResponseData($response);
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

        $this->startApiLog($this->action->uniqueId . ($type ? '/' . $type : ''));
        $post = Yii::$app->request->post();

        switch ($type) {
            case self::TYPE_VOIP_INCOMING:
            case self::TYPE_VOIP_GATHER:
                $response = $this->voiceIncoming($post, $type);
                break;
            case self::TYPE_VOIP_CLIENT:
                $response = $this->voiceClient($post);
                break;
            case self::TYPE_VOIP_CONFERENCE:
                $response = $this->voiceConferenceCallback($post);
                break;
            case self::TYPE_VOIP_CONFERENCE_RECORD:
                $response = $this->voiceConferenceRecordCallback($post);
                break;
            case self::TYPE_VOIP_CONFERENCE_CALL:
                $response = $this->voiceConferenceCallCallback($post);
                break;
            case self::TYPE_VOIP_CONFERENCE_CALL_RECORD:
                $response = $this->voiceConferenceCallRecordCallback($post);
                break;
            case self::TYPE_VOIP_FINISH:
                $response = $this->voiceFinish($post);
                break;
            case self::TYPE_VOIP_RECORD:
                $response = $this->voiceRecord($post);
                break;
            case self::TYPE_VOIP_JOIN_TO_WRONG_CONFERENCE:
                $response = $this->actionCallJoinedToWrongConference($post);
                break;
            default:
                $response = $this->voiceDefault($post);
        }

        return $this->getResponseData($response);
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
        $logExecutionTime = new LogExecutionTime();
        $response = [];

        // Yii::info(VarDumper::dumpAsString($post), 'info\API:Communication:voiceIncoming');

        $clientPhone = null;

        $postCall = $post['call'] ?? [];

        // $ciscoPhoneNumber = \Yii::$app->params['global_phone'];

        if ($postCall) {
            $logExecutionTime->start('voiceIncoming');
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

            $isTrustStirCall = $type === self::TYPE_VOIP_GATHER || (empty($postCall['ForwardedFrom']) && Call::isTrustedVerstat($postCall['StirVerstat'] ?? ''));
            if (!$isTrustStirCall && !empty($postCall['ForwardedFrom'])) {
                $isInternal = PhoneList::find()->andWhere([
                    'pl_phone_number' => $postCall['ForwardedFrom'],
                    'pl_enabled' => true
                ])->exists();
                if ($isInternal) {
                    $isTrustStirCall = true;
                }
            }

            try {
                $this->callService->guardFromInternalCall($postCall);
            } catch (CallFromInternalNumberException $e) {
                $vr = new VoiceResponse();
                $vr->say('You are calling from internal phone number. Your call will be declined.', ['language' => 'en-US']);
                $vr->reject(['reason' => 'busy']);
                return $this->getResponseChownData($vr, 404, 404, 'Sales Communication error: ' . $e->getMessage());
            }

            try {
                $this->callService->guardDeclined($client_phone_number, $postCall, Call::CALL_TYPE_IN);
            } catch (CallDeclinedException $e) {
                $vr = new VoiceResponse();
//                $sayParam = ['language' => 'en-US'];   // ['language' => 'en-US', 'voice' => 'alice']
                //$vr->say('Test', $sayParam);
                $vr->reject(['reason' => 'busy']);
                return $this->getResponseChownData($vr, 404, 404, 'Sales Communication error: ' . $e->getMessage());
            }

            $conferenceRoom = ConferenceRoom::find()->where(['cr_phone_number' => $incoming_phone_number, 'cr_enabled' => true])->orderBy(['cr_id' => SORT_DESC])->limit(1)->one();

            if ($conferenceRoom) {
                return $this->startConference($conferenceRoom, $postCall);
            }

//            $departmentPhone = DepartmentPhoneProject::find()->where(['dpp_phone_number' => $incoming_phone_number, 'dpp_enable' => true])->limit(1)->one();
            $departmentPhone = DepartmentPhoneProject::find()->byPhone($incoming_phone_number, false)->enabled()->limit(1)->one();
            if ($departmentPhone) {
                try {
                    $logExecutionTime->start('CallFilterGuardService');
                    $departmentPhoneProjectParamsService = new DepartmentPhoneProjectParamsService($departmentPhone);
                    $callFilterGuardService = new CallFilterGuardService($client_phone_number, $departmentPhoneProjectParamsService, $this->callService);
                    $logExecutionTime->end();
                    if (!$isTrustStirCall && $callFilterGuardService->isEnable() && !$callFilterGuardService->isTrusted()) {
                        $logExecutionTime->start('CallFilterGuardService:runRepression');
                        $callFilterGuardService->runRepression($postCall);
                        $logExecutionTime->end();
                    }
                } catch (CallDeclinedException $e) {
                    \Yii::warning($e->getMessage(), 'CommunicationController:voiceIncoming:callTerminate');
                    $vr = new VoiceResponse();
                    $vr->reject(['reason' => 'busy']);
                    return CallFilterGuardService::getResponseChownData($vr, 404, 404, $e->getMessage());
                } catch (\Throwable $throwable) {
                    Yii::error(AppHelper::throwableLog($throwable), 'CommunicationController:voiceIncoming:CallFilterGuardService');
                }

                $project = $departmentPhone->dppProject;
                $source = $departmentPhone->dppSource;
                if ($project && !$source) {
                    $source = Sources::find()->where(['project_id' => $project->id, 'default' => true])->one();
                    if ($source) {
                        $departmentPhone->dpp_source_id = $source->id;
                    }
                }
                if (!empty($postCall['flow_department'])) {
                    $departmentId = Department::find()->select('dep_id')->andWhere(['dep_key' => $postCall['flow_department']])->scalar();
                    if ($departmentId) {
                        $departmentId = (int)$departmentId;
                    } else {
                        $departmentId = null;
                        Yii::error([
                            'message' => 'Not found department',
                            'callSid' => $callSid,
                            'flow_department' => $postCall['flow_department'],
                        ], 'CommunicationController:voiceIncoming');
                    }
                } elseif ($departmentPhone->dpp_dep_id) {
                    $departmentId = $departmentPhone->dpp_dep_id;
                } else {
                    $departmentId = null;
                }

                $call_project_id = $departmentPhone->dpp_project_id;
                $call_dep_id = $departmentId;
                $call_source_id = $departmentPhone->dpp_source_id;
                $call_language_id = $departmentPhone->dpp_language_id;

                $ivrEnable = (bool)$departmentPhone->dpp_ivr_enable;

                $callModel = $this->findOrCreateCall(
                    $callSid,
                    $parentCallSid,
                    $postCall,
                    $call_project_id,
                    $call_dep_id,
                    $call_source_id,
                    false,
                    $call_language_id,
                    $departmentPhone->dpp_priority,
                    $departmentPhone->dpp_phone_list_id
                );

                if (!$isTrustStirCall && SettingHelper::isEnableCallLogFilterGuard()) {
                    try {
                        $logExecutionTime->start('CallFilterModel');
                        $callLogFilterGuard = (new CallLogFilterGuardService())->handler($client_phone_number, $callModel);
                        $logExecutionTime->end();
                        if (SettingHelper::callSpamFilterEnabled() && ($callLogFilterGuard->guardSpam(SettingHelper::getCallSpamFilterRate()) || $callLogFilterGuard->guardTrust(SettingHelper::getCallTrustFilterRate()))) {
                            if (CallRedialGuard::guard($callModel->cProject->project_key ?? '', $callModel->cDep->dep_key ?? '')) {
                                $logExecutionTime->start('redial');
                                $result = Yii::$app->comms->twilioDial(
                                    $incoming_phone_number,
                                    $client_phone_number,
                                    SettingHelper::getCallbackToCallerCurlTimeout(),
                                    SettingHelper::getCallbackToCallerMessage(),
                                    SettingHelper::getCallbackToCallerDialCallTimeout(),
                                    SettingHelper::getCallbackToCallerDialCallLimit(),
                                );
                                $logExecutionTime->end();

//                                Yii::info([
//                                    'callId' => $callModel->c_id,
//                                    'rate' => $callLogFilterGuard->clfg_sd_rate,
//                                    'type' => $callLogFilterGuard->getTypeName(),
//                                    'phone' => $callModel->c_from,
//                                    'result' => $result,
//                                ], 'info\CallSpamFilter:DepartmentCall:CallDeclinedException');

                                $redialStatus = $result['data']['result']['status'] ?? null;
                                if ($redialStatus) {
                                    $callLogFilterGuard->setRedialStatusByTwilioStatus($redialStatus);
                                    (new CallLogFilterGuardRepository($callLogFilterGuard))->save();
                                }

                                if (isset($result['data']['is_error']) && $result['data']['is_error'] === true) {
                                    Yii::error([
                                        'callId' => $callModel->c_id,
                                        'rate' => $callLogFilterGuard->clfg_sd_rate,
                                        'type' => $callLogFilterGuard->getTypeName(),
                                        'phone' => $callModel->c_from,
                                        'message' => $result['data']['message']
                                    ], 'CallSpamFilter:DepartmentCall:CommunicationError');
                                } elseif (
                                    !in_array(
                                        $redialStatus,
                                        SettingHelper::getCallbackToCallerSuccessStatusList(),
                                        true
                                    )
                                ) {
                                    return CallFilterGuardService::getResponseChownData($this->returnTwmlAsBusy(SettingHelper::getCallSpamFilterMessage()), 404, 404, SettingHelper::getCallSpamFilterMessage());
                                }
                            }
                        }
                    } catch (\Throwable $throwable) {
                        $logExecutionTime->end();
                        $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), [
                            'phoneFrom' => LogHelper::replaceSource($callModel->c_from, 3),
                            'phoneUid' => CheckPhoneService::uidGenerator($callModel->c_from),
                            'phoneTo' => $callModel->c_to
                        ]);
                        $category = 'CommunicationController:CallLogFilterGuard:generalLine';
                        if ($throwable instanceof DomainException || strpos($throwable->getMessage(), 'Operation timed out')) {
                            Yii::warning($message, $category);
                        } else {
                            Yii::error($message, $category);
                        }
                    }
                }
                if ($type !== self::TYPE_VOIP_GATHER && $logExecutionTime->getResult()) {
                    $this->saveLogExecutionTimeToCallJson($callModel, $logExecutionTime->getResult());
                    $callModel->save(false);
                }

                if ($departmentPhone->dugUgs) {
                    foreach ($departmentPhone->dugUgs as $userGroup) {
                        $exist = CallUserGroup::find()->where([ 'cug_ug_id' => $userGroup->ug_id, 'cug_c_id' => $callModel->c_id])->exists();

                        if ($exist) {
                            continue;
                        }

                        $cug = new CallUserGroup();
                        $cug->cug_ug_id = $userGroup->ug_id;
                        $cug->cug_c_id = $callModel->c_id;
                        //$cug->link('cugUg', $callModel);
                        if (!$cug->save()) {
                            Yii::error(
                                VarDumper::dumpAsString($cug->errors),
                                'API:Communication:voiceIncoming:CallUserGroup:save'
                            );
                        }
                    }
                }

                $callModel->c_source_type_id = Call::SOURCE_GENERAL_LINE;

                if (!empty($postCall['flow_department'])) {
                    return $this->ivrFlowFinish($callModel, $departmentPhone);
                }

                if ($ivrEnable) {
                    $ivrSelectedDigit = isset($postCall['Digits']) ? (int)$postCall['Digits'] : null;
                    $ivrStep = (int)Yii::$app->request->get('step', 1);
                    return $this->ivrService($callModel, $departmentPhone, $ivrStep, $ivrSelectedDigit);
                }

                $response['error'] = 'Not enable IVR';
                $response['error_code'] = 13;
            } else {
//                $upp = UserProjectParams::find()->where(['upp_tw_phone_number' => $incoming_phone_number])->limit(1)->one();
                $upp = UserProjectParams::find()->byPhone($incoming_phone_number, false)->limit(1)->one();
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

                    $callFromInternalPhone = PhoneList::find()->byPhone($client_phone_number)->enabled()->exists();

                    $callModel = $this->findOrCreateCall(
                        $callSid,
                        $parentCallSid,
                        $postCall,
                        $upp->upp_project_id,
                        $call_dep_id,
                        null,
                        $callFromInternalPhone,
                        null,
                        Call::DEFAULT_PRIORITY_VALUE,
                        $upp->upp_phone_list_id
                    );
                    $callModel->c_source_type_id = Call::SOURCE_DIRECT_CALL;

                    if (!$isTrustStirCall && SettingHelper::isEnableCallLogFilterGuard()) {
                        try {
                            $logExecutionTime->start('CallFilterModel');
                            $callLogFilterGuard = (new CallLogFilterGuardService())->handler($client_phone_number, $callModel);
                            $logExecutionTime->end();
                            if (SettingHelper::callSpamFilterEnabled() && ($callLogFilterGuard->guardSpam(SettingHelper::getCallSpamFilterRate()) || $callLogFilterGuard->guardTrust(SettingHelper::getCallTrustFilterRate()))) {
                                if (CallRedialGuard::guard($callModel->cProject->project_key ?? '', $callModel->cDep->dep_key ?? '')) {
                                    $logExecutionTime->start('redial');
                                    $result = Yii::$app->comms->twilioDial(
                                        $incoming_phone_number,
                                        $client_phone_number,
                                        SettingHelper::getCallbackToCallerCurlTimeout(),
                                        SettingHelper::getCallbackToCallerMessage(),
                                        SettingHelper::getCallbackToCallerDialCallTimeout(),
                                        SettingHelper::getCallbackToCallerDialCallLimit(),
                                    );
                                    $logExecutionTime->end();

                                    Yii::info([
                                        'callId' => $callModel->c_id,
                                        'rate' => $callLogFilterGuard->clfg_sd_rate,
                                        'type' => $callLogFilterGuard->getTypeName(),
                                        'phone' => $callModel->c_from,
                                        'result' => $result,
                                    ], 'info\CallSpamFilter:DirectCall:CallDeclinedException');

                                    $redialStatus = $result['data']['result']['status'] ?? null;
                                    if ($redialStatus) {
                                        $callLogFilterGuard->setRedialStatusByTwilioStatus($redialStatus);
                                        (new CallLogFilterGuardRepository($callLogFilterGuard))->save();
                                    }

                                    if (isset($result['data']['is_error']) && $result['data']['is_error'] === true) {
                                        Yii::error([
                                            'callId' => $callModel->c_id,
                                            'rate' => $callLogFilterGuard->clfg_sd_rate,
                                            'type' => $callLogFilterGuard->getTypeName(),
                                            'phone' => $callModel->c_from,
                                            'message' => $result['data']['message']
                                        ], 'CallSpamFilter:DirectCall:CommunicationError');
                                    } elseif (
                                        $redialStatus && !in_array(
                                            $result['data']['result']['status'],
                                            SettingHelper::getCallbackToCallerSuccessStatusList(),
                                            true
                                        )
                                    ) {
                                        return CallFilterGuardService::getResponseChownData($this->returnTwmlAsBusy(SettingHelper::getCallSpamFilterMessage()), 404, 404, SettingHelper::getCallSpamFilterMessage());
                                    }
                                }
                            }
                        } catch (\Throwable $throwable) {
                            $logExecutionTime->end();
                            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), [
                                'phoneFrom' => LogHelper::replaceSource($callModel->c_from, 3),
                                'phoneUid' => CheckPhoneService::uidGenerator($callModel->c_from),
                                'phoneTo' => $callModel->c_to
                            ]);
                            $category = 'CommunicationController:CallLogFilterGuard:directCall';
                            if ($throwable instanceof DomainException || strpos($throwable->getMessage(), 'Operation timed out')) {
                                Yii::warning($message, $category);
                            } else {
                                Yii::error($message, $category);
                            }
                        }
                    }

                    if ($type !== self::TYPE_VOIP_GATHER && $logExecutionTime->getResult()) {
                        $this->saveLogExecutionTimeToCallJson($callModel, $logExecutionTime->getResult());
                        $callModel->save(false);
                    }

                    /** @var Employee $user */
                    $user = $upp->uppUser;

                    if ($user) {
                        if ($upp->upp_vm_enabled && $upp->upp_vm_id) {
                            if (
                                $upp->vmIsAll()
                                || ($upp->vmIsOffline() && !$user->isOnline())
                                || ($upp->vmIsOnline() && $user->isOnline())
                            ) {
                                if (!$callModel->c_client_id) {
                                    try {
                                        $clientForm = ClientCreateForm::createWidthDefaultName();
                                        $clientForm->projectId = $upp->upp_project_id;
                                        $clientForm->typeCreate = Client::TYPE_CREATE_CALL;
                                        $client = (Yii::createObject(ClientManageService::class))->getOrCreateByPhones([new PhoneCreateForm(['phone' => $callModel->c_from])], $clientForm);
                                        $callModel->c_client_id = $client->id;
                                    } catch (\Throwable $e) {
                                        Yii::error($e->getMessage(), 'API:Communication:createVoiceMailResponse:Client:create');
                                    }
                                }
                                $callModel->c_created_user_id = $upp->upp_user_id;
                                if (!$callModel->save()) {
                                    Yii::error(VarDumper::dumpAsString($callModel->getErrors()), 'API:Communication:createVoiceMailResponse:Call:save');
                                }

                                return $this->createVoiceMailResponse($upp->voiceMail, $callModel->c_created_user_id, $callModel->c_client_id);
                            }
                        }

                        if ($user->isOnline()) {
                            if ($callFromInternalPhone && $user->userStatus->us_is_on_call) {
                                return $this->createExceptionCall($incoming_phone_number, 'User is on call');
                            }

                            return $this->createDirectCall($callModel, $user, $callFromInternalPhone);
                        }

                        if ($callFromInternalPhone) {
                            return $this->createExceptionCall($incoming_phone_number, 'User is offline');
                        }

//                      Yii::info('Offline - User (' . $user->username . ') Id: ' . $user->id . ', phone: ' . $incoming_phone_number,
//                            'info\API:Communication:Incoming:Offline');
                        $message = 'Missing Call from ' . $client_phone_number . ' to ' . $incoming_phone_number . "\r\n Reason: Agent offline";
                        if ($callModel->c_lead_id && $callModel->cLead) {
                            $message .= "\r\n Lead (Id: " . Purifier::createLeadShortLink($callModel->cLead) . ")";
                        }
                        if ($callModel->c_case_id && $callModel->cCase) {
                            $message .= "\r\n Case (Id: " . Purifier::createCaseShortLink($callModel->cCase) . ")";
                        }
                        if (
                            $ntf = Notifications::create(
                                $user->id,
                                'Missing Call [Offline]',
                                $message,
                                Notifications::TYPE_WARNING,
                                true
                            )
                        ) {
                            // Notifications::socket($user->id, null, 'getNewNotification', [], true);
                            $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                            Notifications::publish('getNewNotification', ['user_id' => $user->id], $dataNotification);
                        }
                        $callModel->c_source_type_id = Call::SOURCE_REDIRECT_CALL;
                        return $this->createHoldCall($callModel);
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

        if ($callData && isset($callData['CallSid'], $callData['RecordingSid'])) {
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

            if (!$call->isConferenceType()) {
                if ($call->isGeneralParent()) {
                    if ($child = Call::find()->firstChild($call->c_id)->one()) {
                        $call = $child;
                    }
                }
            }

//            Yii::error(VarDumper::dumpAsString(['post' => $post, 'model' => $call->getAttributes()]));;

            if ($call && $callData['RecordingUrl']) {
                //$call->c_recording_url = $callData['RecordingUrl'] ?? null;

//                if (!$call->c_recording_sid && !empty($callData['RecordingUrl'])) {
//                    preg_match('~(RE[0-9a-zA-Z]{32})$~', $callData['RecordingUrl'], $math);
//                    if (!empty($math[1])) {
//                        $call->c_recording_sid = $math[1];
//                    }
//                }

                if (!$call->c_recording_sid && $callData['RecordingSid']) {
                    $call->c_recording_sid = $callData['RecordingSid'];
                }

                $call->c_recording_duration = $callData['RecordingDuration'] ?? null;

                if (!$call->save()) {
                    Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceRecord:Call:save');
                } else {
                    $logEnable = Yii::$app->params['settings']['call_log_enable'] ?? false;
                    if ($logEnable) {
                        if ($call->c_recording_sid) {
                            (Yii::createObject(CallLogTransferService::class))->saveRecord($call);
                        }
                    }
                }

//                if ($call->c_lead_id) {
//                    //if ($call->c_created_user_id) {
//                        // Notifications::create($call->c_created_user_id, 'Call Recording Completed  from ' . $call->c_from . ' to ' . $call->c_to . ' <br>Lead ID: ' . $call->c_lead_id , Notifications::TYPE_INFO, true);
//                    //}
//                    // Notifications::socket(null, $call->c_lead_id, 'recordingUpdate', ['url' => $call->c_recording_url], true);
//
//                    Notifications::sendSocket('recordingUpdate', ['lead_id' => $call->c_lead_id], ['url' => $call->recordingUrl]);
//                }

                if (!empty($callData['voiceMail'])) {
                    $voiceMailRecord = new VoiceMailRecord();
                    $voiceMailRecord->vmr_call_id = $call->c_id;
                    $voiceMailRecord->vmr_record_sid = $callData['RecordingSid'];
                    $voiceMailRecord->vmr_user_id = $callData['userId'] ?? null;
                    $voiceMailRecord->vmr_client_id = $callData['clientId'] ?? null;
                    $voiceMailRecord->vmr_created_dt = !empty($callData['RecordingStartTime']) ? date('Y-m-d H:i:s', strtotime($callData['RecordingStartTime'])) : null;
                    $voiceMailRecord->vmr_duration = (int)$callData['RecordingDuration'];
                    $voiceMailRecord->vmr_new = true;
                    $voiceMailRecord->vmr_deleted = false;
                    if ($voiceMailRecord->save()) {
                        $clientData = '';
                        if ($client = Client::findOne($voiceMailRecord->vmr_client_id)) {
                            $clientData = 'Client(Id: ' . $client->id . ' Name: ' . ($client->is_company ? $client->company_name : $client->getShortName()) . '). ';
                        }
                        $ntf = Notifications::create(
                            $voiceMailRecord->vmr_user_id,
                            'Voice Mail',
                            'New Voice Mail. ' . $clientData . 'Record: ' . $voiceMailRecord->getRecordingUrl(),
                            Notifications::TYPE_INFO,
                            true
                        );
                        if ($ntf) {
                            Notifications::publish('getNewNotification', ['user_id' => $voiceMailRecord->vmr_user_id], NotificationMessage::add($ntf));
                            Notifications::publish('updateVoiceMailRecord', ['user_id' => $voiceMailRecord->vmr_user_id], []);
                        }
                    } else {
                        Yii::error(VarDumper::dumpAsString([
                            'errors' => $voiceMailRecord->getErrors(),
                            'model' => $voiceMailRecord->getAttributes(),
                            'callData' => $callData,
                        ]), 'API:Communication:voiceRecord:VoiceMailRecord:save');
                    }
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

                if (!$call->save()) {
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

        // Yii::info(VarDumper::dumpAsString($post), 'info\API:Communication:voiceClient');

        // $post['callData']['CallSid']

        $callSid = $post['callData']['sid'] ?? $post['callData']['CallSid'] ?? null;

        if ($callSid) {
            $call = Call::find()->where(['c_call_sid' => $callSid])->orderBy(['c_id' => SORT_ASC])->limit(1)->one();

            $callData = $post['call'];
            $callOriginalData = $post['callData'] ?? [];

            if (!$call) {
                $call = new Call();
                $call->c_call_sid = $callSid;
                $call->c_call_type_id = (int) $callData['c_call_type_id'];

                if (isset($callOriginalData['ParentCallSid'])) {
                    $call->c_parent_call_sid = $callOriginalData['ParentCallSid'];
                }

                $call->c_from = $callOriginalData['From'] ?? null;
                $call->c_to = $callOriginalData['To'] ?? null;
                $call->c_caller_name = $callOriginalData['Caller'] ?? null;
                if (VoipDevice::isValid($call->c_from)) {
                    $agentId = VoipDevice::getUserId($call->c_from);
                } else {
                    $agentId = null;
                }

                if (!empty($callOriginalData['c_project_id'])) {
                    $call->c_project_id = (int)$callOriginalData['c_project_id'];
                } elseif (!empty($callData['c_project_id'])) {
                    $call->c_project_id = (int)$callData['c_project_id'];
                }

                if (!empty($callOriginalData['c_dep_id'])) {
                    $call->c_dep_id = (int)$callOriginalData['c_dep_id'];
                }

                if (!empty($callOriginalData['c_source_type_id'])) {
                    $call->c_source_type_id = (int)$callOriginalData['c_source_type_id'];
                }

                if (!empty($callOriginalData['lead_id']) && $callOriginalData['lead_id'] !== 'null') {
                    $call->c_lead_id = (int) $callOriginalData['lead_id'];
                }

                if (!empty($callOriginalData['case_id']) && $callOriginalData['case_id'] !== 'null') {
                    $call->c_case_id = (int) $callOriginalData['case_id'];
//                if ($call->c_case_id && ($case = Cases::findOne($call->c_case_id))) {
//                    (Yii::createObject(CasesCommunicationService::class))->processIncoming($case, CasesCommunicationService::TYPE_PROCESSING_CALL);
//                }
                }

                $upp = null;

                if (!$call->c_dep_id) {
                    if ($call->c_lead_id && ($lead = $call->cLead)) {
                        $call->c_dep_id = $lead->l_dep_id;
                    } elseif ($call->c_case_id && ($case = $call->cCase)) {
                        $call->c_dep_id = $case->cs_dep_id;
                    } elseif ($call->c_project_id && !empty($callOriginalData['FromAgentPhone'])) {
                        $upp = UserProjectParams::find()->byPhone($callOriginalData['FromAgentPhone'], false)->andWhere(['upp_project_id' => $call->c_project_id])->limit(1)->one();
                        if ($upp && $upp->upp_dep_id) {
                            $call->c_dep_id = $upp->upp_dep_id;
                        }
                    }
                }

                if (!empty($callOriginalData['c_client_id'])) {
                    $call->c_client_id = (int)$callOriginalData['c_client_id'];
                }

                if ($call->isOut()) {
                    if (!$call->c_client_id && $call->c_to) {
                        $clientPhone = ClientPhone::find()->where(['phone' => $call->c_to])->orderBy(['id' => SORT_DESC])->limit(1)->one();
                        if ($clientPhone && $clientPhone->client_id) {
                            $call->c_client_id = $clientPhone->client_id;
                        }
                    }

                    if ($callOriginalData['c_user_id']) {
                        Yii::createObject(LeadRedialUnAssigner::class)->createCall((int)$callOriginalData['c_user_id']);
                    }
                }

                if (!$upp && $call->c_project_id && $agentId) {
                    $upp = UserProjectParams::find()->where(['upp_user_id' => $agentId, 'upp_project_id' => $call->c_project_id])->limit(1)->one();
                }

                if (!$upp) {
                    $upp = UserProjectParams::find()->byPhone($call->c_from, false)->one();
                }

                if ($upp && $upp->uppUser) {
                    $call->c_created_user_id = $upp->uppUser->id;
                    $call->c_project_id = $upp->upp_project_id;

                    if (!$call->c_dep_id) {
                        $call->c_dep_id = $upp->upp_dep_id;
                    }
                }

                if (!$call->c_created_user_id) {
                    $call->c_created_user_id = $agentId;
                }
                // Yii::warning('Not found Call: ' . $callSid, 'API:Communication:voiceClient:Call::find');

                $call->c_recording_disabled = (bool) ($callOriginalData['call_recording_disabled'] ?? false);

                $call->setDataCreatorType((int)($callOriginalData['creator_type_id'] ?? null));
            }

            if (!empty($callOriginalData['CallStatus'])) {
                $call->c_call_status = $callOriginalData['CallStatus'];
                $call->setStatusByTwilioStatus($call->c_call_status);
            }

            if (!$call->c_call_status) {
                Yii::warning('Not found status Call: ' . $callSid . ', ' . VarDumper::dumpAsString($callOriginalData), 'API:Communication:voiceClient:Call::status');
            }

            if (!empty($callOriginalData['is_conference_call']) && !$call->isConferenceType()) {
                $call->setConferenceType();
            }

            if (!empty($callOriginalData['phone_list_id'])) {
                $call->setDataPhoneListId((int)$callOriginalData['phone_list_id']);
            }

            if (!empty($callOriginalData['FromAgentPhone'])) {
                $call->c_from = $callOriginalData['FromAgentPhone'];
            }

            $call->setDataCreatedParams($callOriginalData);


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

//            if($call->c_lead_id && $lead = $call->cLead) {
//                if ($lead->isPending() && $lead->isCallProcessing()) {
//
//                    $delayTimeMin = $lead->getDelayPendingTime();
//                    $lead->l_pending_delay_dt = date('Y-m-d H:i:s', strtotime('+' . $delayTimeMin . ' minutes'));
//                    $lead->employee_id = null;
//                    $lead->callReady();
//
//                    if (!$lead->save()) {
//                        Yii::error('lead: ' . $lead->id . ' ' . VarDumper::dumpAsString($lead->errors), 'API:Communication:voiceClient:Lead:save');
//                    }
//                }
//
//                if ($lead->isProcessing() && !$lead->isCallDone()) {
//                    $lead->callDone();
//                    if (!$lead->save()) {
//                        Yii::error('lead: ' . $lead->id . ' ' . VarDumper::dumpAsString($lead->errors), 'API:Communication:voiceClient:Lead:save2');
//                    }
//                }
//            }
            if (!$call->save()) {
                Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceClient:Call:save');
            }
        } else {
            Yii::error('Communication Request: Not found post[callData][sid] / post[callData][CallSid] ' . VarDumper::dumpAsString($post), 'API:Communication:voiceClient:post');
        }

        return $response;
    }

    private function processCallAfterUniqueError(Call $call): Call
    {
        $callSid = $call->c_call_sid;
        /** @var Call $oldCall */
        $oldCall = Call::find()->andWhere(['c_call_sid' => $callSid])->one();
        if (!$oldCall) {
            throw new UniqueCallNotFoundException($callSid);
        }
        if ($oldCall->isTwFinishStatus()) {
            throw new CallFinishedException($oldCall->c_call_sid);
        }
        if ($oldCall->isStatusInProgress() && $call->isStatusRinging()) {
            $call = $oldCall;
            Yii::info([
                'message' => 'Detected Ringing call after InProgress',
                'callSid' => $call->c_call_sid
            ], 'info\ProcessCallCallback');
        } else {
            self::copyUpdatedData($call, $oldCall);
            $call = $oldCall;
            if (!$call->save()) {
                \Yii::error([
                    'message' => 'Copy new Call to old Call',
                    'errors' => $call->getErrors(),
                    'call' => $call->getAttributes(),
                ], 'info\ProcessCallCallback');
            }
        }

        Yii::info([
            'message' => 'Detected duplicate call',
            'callSid' => $call->c_call_sid
        ], 'info\ProcessCallCallback');
        return $call;
    }

    /**
     * @param array $post
     * @return array
     * @throws \Exception
     */
    private function voiceDefault(array $post = []): array
    {
        $response = [
            'trace' => [],
            'error' => '',
        ];

        if (empty($post['callData']['CallSid'])) {
            Yii::error('Not found POST[callData][CallSid] ' . VarDumper::dumpAsString($post), 'API:Communication:voiceDefault:callData:notFound');
            $response['error'] = 'Error in method voiceDefault. Not found POST[callData][CallSid]';
            $response['status'] = 'Fail';
            return $response;
        }

        $callData = $post['callData'];
        try {
            $call = $this->findOrCreateCallByData($callData);

            $callSaved = false;
            $isNewRecord = $call->isNewRecord;

            $call->validate();

            if ($errors = $call->getErrors()) {
                $isOnlyUniqueError = $this->checkOnlyUniqueCallSidError($errors, $call->c_call_sid);
                if ($isOnlyUniqueError) {
                    $call = $this->processCallAfterUniqueError($call);
                } else {
                    \Yii::error([
                        'errors' => $errors,
                        'call' => $call->getAttributes(),
                    ], 'API:CommunicationController:findOrCreateCallByData:Call:save');
                }
            } else {
                try {
                    $call->save(false);
                    $callSaved = true;
                } catch (\yii\db\IntegrityException $e) {
                    if (!empty($e->errorInfo[2]) && strpos($e->errorInfo[2], 'Duplicate entry', 0) === 0) {
                        $call = $this->processCallAfterUniqueError($call);
                    } else {
                        throw $e;
                    }
                }
            }

            if ($isNewRecord && $callSaved) {
                if (($parentCall = $call->cParent) && $parentCall->callUserGroups && !$call->callUserGroups) {
                    foreach ($parentCall->callUserGroups as $cugItem) {
                        $cug = new CallUserGroup();
                        $cug->cug_ug_id = $cugItem->cug_ug_id;
                        $cug->cug_c_id = $call->c_id;
                        if (!$cug->save()) {
                            \Yii::error(
                                VarDumper::dumpAsString($cug->errors),
                                'API:CommunicationController:findOrCreateCallByData:CallUserGroup:save'
                            );
                        }
                    }
                }
            }
        } catch (UniqueCallNotFoundException $e) {
            Yii::error([
                'message' => $e->getMessage(),
                'post' => $post,
            ], 'CallCallBackProcessingError');
            $response['status'] = 'Success';
            return $response;
        } catch (CallFinishedException $e) {
            Yii::info([
                'message' => $e->getMessage(),
                'callSid' => $e->callSid,
                'post' => $post,
            ], 'log\CallCallBackProcessingError');
            $response['status'] = 'Success';
            return $response;
        } catch (\Throwable $e) {
            Yii::error([
                'message' => $e->getMessage(),
                'post' => $post,
            ], 'CallCallBackProcessingError');
            $response['status'] = 'Success';
            return $response;
        }

        if (!$call->isJoin()) {
            if ($call->isStatusNoAnswer() || $call->isStatusBusy() || $call->isStatusCanceled() || $call->isStatusFailed()) {
                if ($call->c_lead_id) {
                    if (($lead = $call->cLead) && !$lead->isCallCancel()) {
                        try {
                            $leadRepository = Yii::createObject(LeadRepository::class);
                            $lead->callCancel();
                            $leadRepository->save($lead);
                        } catch (\Throwable $e) {
                            Yii::error('LeadId: ' . $lead->id . ' Message: ' . $e->getMessage(), 'API:Communication:voiceDefault:Lead:save');
                            $response['error'] = 'Error in method voiceDefault. LeadId: ' . $lead->id . ' Message: ' . $e->getMessage();
                        }
                    }
                }
            }
        }

        if (!empty($callData['accepted_call_sid']) && $call->c_created_user_id && $call->isStatusInProgress()) {
            Notifications::publish(RemoveIncomingRequestMessage::COMMAND, ['user_id' => $call->c_created_user_id], RemoveIncomingRequestMessage::create($callData['accepted_call_sid']));
        }

        $response['status'] = 'Success';

        return $response;
    }


    /**
     * @param string $callSid
     * @param string|null $parentCallSid
     * @param array $calData
     * @param int $call_project_id
     * @param int|null $call_dep_id
     * @param int|null $call_source_id
     * @param bool $callFromInternalPhone
     * @param string|null $callLanguageId
     * @param int $priority
     * @param int $phoneListId
     * @return Call
     * @throws \Exception
     */
    protected function findOrCreateCall(
        string $callSid,
        ?string $parentCallSid,
        array $calData,
        int $call_project_id,
        ?int $call_dep_id,
        ?int $call_source_id,
        bool $callFromInternalPhone,
        ?string $callLanguageId,
        int $priority,
        ?int $phoneListId
    ): Call {
        $call = null;
        $parentCall = null;
        $clientId = null;

        //error_log("Call Data: " . print_r($calData, true));

        if (isset($calData['From']) && $calData['From']) {
            $clientPhoneNumber = $calData['From'];
            if ($clientPhoneNumber && !ContactPhoneListService::isInvalid($clientPhoneNumber)) {
                $client = ClientsQuery::oneByPhoneAndProject($clientPhoneNumber, $call_project_id, null);
                if ($client) {
                    /** @var Client $client */
                    $clientId = $client->id;
                }
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
            $call->setTypeIn();
            // $call->c_call_status = Call::TW_STATUS_IVR; //$calData['CallStatus'] ?? Call::CALL_STATUS_QUEUE;
            if (!$callFromInternalPhone) {
                $call->setStatusIvr();
            }

            $call->c_com_call_id = $calData['c_com_call_id'] ?? null;
            $call->c_parent_call_sid = $calData['ParentCallSid'] ?? null;
            $call->c_offset_gmt = Call::getClientTime($calData);
            $call->c_from_country = Call::getDisplayRegion($calData['FromCountry'] ?? '');
            $call->c_from_state = $calData['FromState'] ?? null;
            $call->c_from_city = $calData['FromCity'] ?? null;
            $call->c_stir_status = Call::getStirStatusByVerstatKey($calData['StirVerstat'] ?? '');

            if ($parentCall) {
                $call->c_parent_id = $parentCall->c_id;
                $call->c_project_id = $parentCall->c_project_id;
                $call->c_dep_id = $parentCall->c_dep_id;
                $call->c_source_type_id = $parentCall->c_source_type_id;
                $call->c_language_id = $parentCall->c_language_id;
                $call->c_group_id = $parentCall->c_group_id;
                $call->c_queue_start_dt = $parentCall->c_queue_start_dt;
                $call->c_stir_status = $parentCall->c_stir_status;

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
                //$call->c_u_id = $parentCall->c_dep_id;

                if (!empty($call->c_stir_status) && empty($parentCall->c_stir_status)) {
                    $parentCall->c_stir_status = $call->c_stir_status;
                    $parentCall->save();
                }
            }

            if ($call_project_id) {
                $call->c_project_id = $call_project_id;
            }
            if ($call_dep_id) {
                $call->c_dep_id = $call_dep_id;
            }

            if ($callLanguageId) {
                $call->c_language_id = $callLanguageId;
            }

            $call->c_is_new = true;
            $call->c_created_dt = date('Y-m-d H:i:s');
            $call->c_from = $calData['From'];
            if (!empty($calData['ForwardedFrom']) && SettingHelper::isOverridePhoneToForwarderFrom() && ($forwardedPhoneListId = PhoneList::find()->select(['pl_id'])->byPhone($calData['ForwardedFrom'])->scalar())) {
                $call->c_to = $calData['ForwardedFrom'];
                $call->setDataPhoneListId((int)$forwardedPhoneListId);
            } else {
                $call->c_to = $calData['To'];
                $call->setDataPhoneListId($phoneListId);
            }
            $call->c_created_user_id = null;

            if ($clientId) {
                $call->c_client_id = $clientId;
            }

//            if ($call->c_dep_id === Department::DEPARTMENT_SALES) {
//                /*$lead = Lead::findLastLeadByClientPhone($call->c_from, $call->c_project_id);
//                if ($lead) {
//                    $call->c_lead_id = $lead->id;
//                }*////
//            } elseif ($call->c_dep_id === Department::DEPARTMENT_EXCHANGE || $call->c_dep_id === Department::DEPARTMENT_SUPPORT) {
//
//            }

            $call->c_recording_disabled = (bool)($calData['call_recording_disabled'] ?? false);
            $call->setDataPriority($priority);
            $call->setDataCreatedParams($calData);
            $call->setDataCreatorType((int)($calData['creator_type_id'] ?? null));

            if (!$call->save()) {
                \Yii::error(VarDumper::dumpAsString($call->errors), 'API:CommunicationController:findOrCreateCall:Call:save');
                throw new \Exception('findOrCreateCall: Can not save call in db', 1);
            } else {
                if ($parentCall && $parentCall->callUserGroups && !$call->callUserGroups) {
                    foreach ($parentCall->callUserGroups as $cugItem) {
                        $cug = new CallUserGroup();
                        $cug->cug_ug_id = $cugItem->cug_ug_id;
                        $cug->cug_c_id = $call->c_id;
                        if (!$cug->save()) {
                            \Yii::error(VarDumper::dumpAsString($cug->errors), 'API:CommunicationController:findOrCreateCall:CallUserGroup:save');
                        }
                    }
                }
            }
        }

        return $call;
    }

    private function checkOnlyUniqueCallSidError(array $errors, $callSid): bool
    {
        if (!$callSid) {
            return false;
        }

        $callSid = (string)$callSid;

        if (count($errors) > 1) {
            return false;
        }

        if (empty($errors['c_call_sid'])) {
            return false;
        }

        if (count($errors['c_call_sid']) > 1) {
            return false;
        }

        if ($errors['c_call_sid'][0] === 'Call SID "' . $callSid . '" has already been taken.') {
            return true;
        }

        return false;
    }

    private static function copyDataFromParentCall(Call $call, Call $parentCall): void
    {
        $call->c_parent_id = $parentCall->c_id;
        $call->c_project_id = $parentCall->c_project_id;
        $call->c_dep_id = $parentCall->c_dep_id;
        $call->c_source_type_id = $parentCall->c_source_type_id;
        $call->c_lead_id = $parentCall->c_lead_id;
        $call->c_case_id = $parentCall->c_case_id;
        $call->c_client_id = $parentCall->c_client_id;
        $call->c_group_id = $parentCall->c_group_id;
        $call->c_queue_start_dt = $parentCall->c_queue_start_dt;
        $call->c_created_user_id = $parentCall->c_created_user_id;
        $call->c_call_type_id = $parentCall->c_call_type_id;
        $call->c_conference_id = $parentCall->c_conference_id;
        $call->c_conference_sid = $parentCall->c_conference_sid;
        $call->c_stir_status = $parentCall->c_stir_status;
    }

    private static function copyDataFromCustomParams(Call $call, CallCustomParameters $customParameters): void
    {
        if ($customParameters->type_id) {
            if ($customParameters->type_id === Call::CALL_TYPE_JOIN) {
                $call->setTypeJoin();
            } elseif ($customParameters->type_id === Call::CALL_TYPE_OUT) {
                $call->setTypeOut();
            } elseif ($customParameters->type_id === Call::CALL_TYPE_IN) {
                $call->setTypeIn();
            } elseif ($customParameters->type_id === Call::CALL_TYPE_RETURN) {
                $call->setTypeReturn();
            }
        }

        if ($customParameters->is_conference_call) {
            $call->setConferenceType();
        }

        if ($customParameters->source_type_id) {
            $call->c_source_type_id = $customParameters->source_type_id;
        }

        if ($customParameters->project_id) {
            $call->c_project_id = $customParameters->project_id;
        }

        if ($customParameters->lead_id) {
            $call->c_lead_id = $customParameters->lead_id;
        }

        if ($customParameters->case_id) {
            $call->c_case_id = $customParameters->case_id;
        }

        if ($customParameters->user_id) {
            $call->c_created_user_id = $customParameters->user_id;
        }

        if ($customParameters->from) {
            $call->c_from = $customParameters->from;
        }

        if ($customParameters->to) {
            $call->c_to = $customParameters->to;
        } else {
            if (!$call->isInternal()) {
                if (VoipDevice::isValid($call->c_to)) {
                    $call->c_to = null;
                }
            }
        }

        $call->c_recording_disabled = $customParameters->call_recording_disabled;

        if ($customParameters->phone_list_id) {
            $call->setDataPhoneListId($customParameters->phone_list_id);
        }

        if ($customParameters->is_warm_transfer) {
            $call->setTypeIn();
            $call->c_source_type_id = Call::SOURCE_DIRECT_CALL;
        }

        if ($customParameters->dep_id) {
            $call->c_dep_id = $customParameters->dep_id;
        }

        if ($customParameters->client_id) {
            $call->c_client_id = $customParameters->client_id;
        }

        if ($customParameters->creator_type_id) {
            $call->setDataCreatorType($customParameters->creator_type_id);
        }
    }

    private static function copyUpdatedData(Call $from, Call $to): void
    {
        $to->c_call_status = $from->c_call_status;
        $to->c_status_id = $from->c_status_id;
        $to->c_created_user_id = $from->c_created_user_id;
        $to->c_sequence_number = $from->c_sequence_number;
        $to->c_call_duration = $from->c_call_duration;
        $to->c_forwarded_from = $from->c_forwarded_from;
        $to->c_recording_sid = $from->c_recording_sid;
        $to->c_is_conference = $from->c_is_conference;
    }

    private function getCustomParameters($callData): CallCustomParameters
    {
        $customParameters = new CallCustomParameters();
        $customParameters->load($callData);
        if (!$customParameters->validate()) {
            Yii::error(VarDumper::dumpAsString([
                'message' => 'Call custom parameters error',
                'errors' => $customParameters->getErrors(),
                'model' => $customParameters->getAttributes(),
            ]), 'API:CommunicationController:findOrCreateCallByData');
        }
        $customParameters->resetErrorsAttribute();
        return $customParameters;
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
        $callSid = $callData['CallSid'] ?? '';
        $stirStatus = $callData['StirStatus'] ?? null;

        if ($callSid) {
            $call = Call::find()->where(['c_call_sid' => $callSid])->limit(1)->one();
            if ($call && $call->isDeclined()) {
                $call->c_call_status = $callData['CallStatus'];
                return $call;
            }
        }

        $customParameters = $this->getCustomParameters($callData);

        $parentCallSid = $callData['ParentCallSid'] ?? null;
        if (!$parentCallSid && $customParameters->parent_call_sid) {
            $parentCallSid = $customParameters->parent_call_sid;
        }
        if ($parentCallSid) {
            $parentCall = Call::find()->where(['c_call_sid' => $parentCallSid])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();
        }

        if (!$call) {
            $call = new Call();
            $call->c_call_sid = $callData['CallSid'] ?? null;
            $call->c_parent_call_sid = $parentCallSid;
            $call->c_com_call_id = $callData['c_com_call_id'] ?? null;
            $call->setTypeIn();

            $call->c_stir_status = Call::getStirStatusByVerstatKey($callData['StirVerstat'] ?? '');
            if ($parentCall) {
                self::copyDataFromParentCall($call, $parentCall);

                if (!empty($stirStatus) && empty($parentCall->c_stir_status)) {
                    $parentCall->c_stir_status = $call->c_stir_status;
                    $parentCall->save();
                }
            }

            $call->c_is_new = true;
            $call->c_created_dt = date('Y-m-d H:i:s');
            $call->c_from = $callData['From'];
            $call->c_to = $callData['To']; //Called
            $call->c_created_user_id = null;

            self::copyDataFromCustomParams($call, $customParameters);

            $call->setDataCreatedParams($callData);
        }

        $preCallStatus = $call->c_call_status;

        $isManualChangeStatus = !empty($callData['Command']) && $callData['Command'] === 'change_call_status';

        if ($isManualChangeStatus) {
            if ($call->isStatusRinging()) {
                $call->c_call_status = $callData['CallStatus'];
            }
            if (!empty($callData['IsRedialCall'])) {
                Yii::$app->queue_job->delay(SettingHelper::getRedialAutoTakeSeconds())->push(new AutoTakeJob($call->c_id));
            }
        } else {
            $call->c_call_status = $callData['CallStatus'];
        }

        if ($preCallStatus === Call::TW_STATUS_IN_PROGRESS && $call->c_call_status === Call::TW_STATUS_RINGING) {
            $call->c_call_status = Call::TW_STATUS_IN_PROGRESS;
        }

        if (!$isManualChangeStatus && $call->c_call_status === Call::TW_STATUS_IN_PROGRESS && $call->creatorTypeIsAgent() && $call->isOut()) {
            $call->setStatusRinging();
        } else {
            $call->setStatusByTwilioStatus($call->c_call_status);
        }

        $agentId = null;

        if (!empty($callData['Called']) && VoipDevice::isValid($callData['Called'])) {
            $agentId = VoipDevice::getUserId($callData['Called']);
        }

        if (!$agentId && !empty($callData['c_user_id'])) {
            $agentId = (int) $callData['c_user_id'];
        }

        if ($customParameters->user_id) {
            $call->c_created_user_id = $customParameters->user_id;
        } elseif ($agentId) {
            $call->c_created_user_id = $agentId;
        }

        if (!$call->c_created_user_id && $parentCall && $call->isOut()) {
            $call->c_created_user_id = $parentCall->c_created_user_id;
        }

        if (!empty($callData['SequenceNumber'])) {
            $call->c_sequence_number = (int) $callData['SequenceNumber'];
        }

        if (!empty($callData['CallDuration'])) {
            $call->c_call_duration = (int) $callData['CallDuration'];
        }

        if (!empty($callData['ForwardedFrom'])) {
            $call->c_forwarded_from = $callData['ForwardedFrom'];
        }

        if (!$call->c_recording_sid && !empty($callData['RecordingSid'])) {
            $call->c_recording_sid = $callData['RecordingSid'];
        }

        if ($customParameters->is_conference_call && !$call->isConferenceType()) {
            $call->setConferenceType();
        }

        return $call;
    }


    /**
     * @param Call $callModel
     * @param Employee $user
     * @param bool $callFromInternalPhone
     * @return array
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    protected function createDirectCall(Call $callModel, Employee $user, bool $callFromInternalPhone = false): array
    {
        $jobId = null;
        $callModel->c_created_user_id = $user->id;
        $callModel->c_source_type_id = Call::SOURCE_DIRECT_CALL;

        if (!$callModel->update()) {
            Yii::error(VarDumper::dumpAsString($callModel->errors), 'API:Communication:createDirectCall:Call:update');
        } else {
            $delayJob = 7;
            $job = new CallQueueJob();
            $job->call_id = $callModel->c_id;
            $job->delay = 0;
            $job->delayJob = $delayJob;
            $jobId = Yii::$app->queue_job->delay($delayJob)->priority(90)->push($job);
        }

        $project = $callModel->cProject;

        $responseTwml = new VoiceResponse();
        $callInfo = [];
        $response = [];

        if ($project && !$callFromInternalPhone) {
            $callParams = $project->getParams()->call;
            if ($callParams->play_direct_message) {
                $responseTwml->play($callParams->play_direct_message);
            } else {
                if ($callParams->say_direct_message) {
                    $responseTwml->say($callParams->say_direct_message, [
                        'language' => 'en-US',
                        'voice' => 'alice'
                    ]);
                }
            }
            if ($callParams->url_music_play_hold) {
                $responseTwml->play($callParams->url_music_play_hold, ['loop' => 0]);
            }
        } elseif ($callFromInternalPhone) {
            Yii::error([
                'message' => 'Call from internal number. Deprecated logic.',
                'userId' => $user->id,
                'callId' => $callModel->c_id,
            ], 'CommunicationController:createDirectCall');
            // todo may be is deprecated feature
//            $response['agent_username'][] = prefix device identity + userId.  ex: produser100;
            $response['agent_username'][] = '';
            $responseTwml = null;
        }

        $callInfo['id'] = $callModel->c_id;
        $callInfo['project_id'] = $callModel->c_project_id;
        $callInfo['dep_id'] = $callModel->c_dep_id;
        $callInfo['status'] = $callModel->c_call_status;
        $callInfo['status_id'] = $callModel->c_status_id;
        $callInfo['source_type'] = $callModel->c_source_type_id;



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
     * @return array
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    protected function createHoldCall(Call $callModel): array
    {
        $callModel->c_created_user_id = null;
        $callModel->c_source_type_id = Call::SOURCE_REDIRECT_CALL;

        if (!$callModel->update()) {
            Yii::error(VarDumper::dumpAsString($callModel->errors), 'API:Communication:createDirectCall:Call:update');
        } else {
            $delayJob = 7;
            $job = new CallQueueJob();
            $job->call_id = $callModel->c_id;
            $job->delay = 0;
            $job->delayJob = $delayJob;
            $jobId = Yii::$app->queue_job->delay($delayJob)->priority(100)->push($job);
        }


        $project = $callModel->cProject;

        $responseTwml = new VoiceResponse();

        if ($project) {
            $callParams = $project->getParams()->call;
            if ($callParams->play_redirect_message) {
                $responseTwml->play($callParams->play_redirect_message);
            } else {
                if ($callParams->say_redirect_message) {
                    $responseTwml->say($callParams->say_redirect_message, [
                        'language' => 'en-US',
                        'voice' => 'alice'
                    ]);
                }
            }
            if ($callParams->url_music_play_hold) {
                $responseTwml->play($callParams->url_music_play_hold, ['loop' => 0]);
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
     * @param string $message
     * @return array
     */
    protected function createExceptionCall(string $phoneNumber, string $message = 'Sorry, this number is temporarily not working.'): array
    {
//        Yii::error('Number is temporarily not working ('.$phoneNumber.')', 'API:Communication:createExceptionCall');

        $responseTwml = new VoiceResponse();
        $responseTwml->say($message, [
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

    protected function createVoiceMailResponse(UserVoiceMail $voiceMail, int $userId, ?int $clientId): array
    {
        return [
            'status' => 200,
            'name' => 'Success',
            'code' => 0,
            'message' => '',
            'data' => [
                'response' => [
                    'voiceMail' => $voiceMail->getResponse(),
                    'userId' => $userId,
                    'clientId' => $clientId,
                ]
            ]
        ];
    }

    protected function startCallService(
        Call $callModel,
        DepartmentPhoneProject $department,
        int $ivrSelectedDigit,
        array $stepParams,
        array $repeatParams,
        QueueLongTimeNotificationParams $queueLongTimeParams
    ): array {
        $selectedDepartment = null;
        $dParams = @json_decode($department->dpp_params, true);
        $overrideDepartment = $dParams['overrideDepartment'] ?? [];
        if ($overrideDepartment) {
            if (array_key_exists($ivrSelectedDigit, $overrideDepartment)) {
                $selectedDepartment = $overrideDepartment[$ivrSelectedDigit];
                if (!isset(Department::DEPARTMENT_LIST[$selectedDepartment])) {
                    $selectedDepartment = null;
                }
            }
        }
        if ($selectedDepartment === null) {
            if (isset(Department::DEPARTMENT_LIST[$ivrSelectedDigit])) {
                $selectedDepartment = $ivrSelectedDigit;
            }
        }

        if ($selectedDepartment !== null) {
            $callModel->c_dep_id = $selectedDepartment;
            if (!$callModel->save()) {
                Yii::error(VarDumper::dumpAsString($callModel->errors), 'API:Communication:startCallService:Call:update');
            }

            $delayJob = 7;
            $job = new CallQueueJob();
            $job->call_id = $callModel->c_id;
            $job->source_id = $department->dpp_source_id;
            $job->delay = 0;
            $job->delayJob = $delayJob;
            $jobId = Yii::$app->queue_job->delay($delayJob)->priority(100)->push($job);

            try {
                if (!$jobId) {
                    throw new \DomainException('Not created CallQueueJob');
                }
                if ($repeatParams) {
                    (new RepeatMessageCallJobCreator())->create($callModel, $department->dpp_id, $repeatParams);
                }
                if ($queueLongTimeParams->isActive()) {
                    (new QueueLongTimeNotificationJobCreator())->create($callModel, $department->dpp_id, $queueLongTimeParams->getDelay() + 7);
                }
            } catch (\Throwable $e) {
                Yii::error([
                    'message' => 'Create call job Error.',
                    'useCase' => 'Processing Incoming call. StartCallService',
                    'error' => $e->getMessage(),
                    'call' => $callModel->getAttributes(),
                ], 'CallQueueStartCallService::JobsCreate');
            }
        }

        $choice = $stepParams['digits'][$ivrSelectedDigit] ?? null;
        $responseTwml = new VoiceResponse();

        if (isset($stepParams['before_say']) && $stepParams['before_say']) {
            $responseTwml->say($stepParams['before_say'], ['language' => $stepParams['language'], 'voice' => $stepParams['voice']]);
        }

        if ($choice) {
            if (isset($choice['pause']) && $choice['pause']) {
                $responseTwml->pause(['length' => $choice['pause']]);
            }
            if (isset($choice['say'])) {
                $responseTwml->say($choice['say'], ['language' => $choice['language'], 'voice' => $choice['voice']]);
            }

            if (isset($stepParams['after_say']) && $stepParams['after_say']) {
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
            $repeatParams = $dParams['queue_repeat'] ?? [];
            $queueLongTimeParams = new QueueLongTimeNotificationParams(empty($dParams['queue_long_time_notification']) ? [] : $dParams['queue_long_time_notification']);

            $stepParams = [];

            if (isset($ivrParams['steps'][$ivrStep])) {
                $stepParams = $ivrParams['steps'][$ivrStep];
            }


            $company = '';
            if ($callModel->cProject && $callModel->cProject->name) {
                $company = ' ' . strtolower($callModel->cProject->name);
            }


            if ($ivrStep === 2) {
                $ivrSelectedDigit = (int) $ivrSelectedDigit;

                if ($ivrSelectedDigit) {
                    return $this->startCallService($callModel, $department, $ivrSelectedDigit, $stepParams, $repeatParams, $queueLongTimeParams);
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

            if (isset($ivrParams['entry_pause']) && $ivrParams['entry_pause']) {
                $responseTwml->pause(['length' => $ivrParams['entry_pause']]);
            }

            $entry_phrase = isset($ivrParams['entry_phrase']) ? str_replace('{{project}}', $company, $ivrParams['entry_phrase']) : null;

            if ($entry_phrase) {
                $responseTwml->say($entry_phrase, ['language' => $ivrParams['entry_language'], 'voice' => $ivrParams['entry_voice']]);
            }


            if (isset($ivrParams['steps'])) {
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
                if (isset(Department::DEPARTMENT_LIST[$department->dpp_dep_id])) {
                    $callModel->c_dep_id = $department->dpp_dep_id;
                    if (!$callModel->save()) {
                        Yii::error(VarDumper::dumpAsString($callModel->errors), 'API:Communication:startCallService:Call:update2');
                    }

                    $delayJob = 7;
                    $job = new CallQueueJob();
                    $job->call_id = $callModel->c_id;
                    $job->source_id = $department->dpp_source_id;
                    $job->delay = 0;
                    $job->delayJob = $delayJob;
                    $jobId = Yii::$app->queue_job->delay($delayJob)->priority(80)->push($job);

                    try {
                        if (!$jobId) {
                            throw new \DomainException('Not created CallQueueJob');
                        }
                        if ($repeatParams) {
                            (new RepeatMessageCallJobCreator())->create($callModel, $department->dpp_id, $repeatParams);
                        }
                        if ($queueLongTimeParams->isActive()) {
                            (new QueueLongTimeNotificationJobCreator())->create($callModel, $department->dpp_id, $queueLongTimeParams->getDelay() + 7);
                        }
                    } catch (\Throwable $e) {
                        Yii::error([
                            'message' => 'Create call job Error.',
                            'useCase' => 'Processing Incoming call. Without ivrSteps params',
                            'error' => $e->getMessage(),
                            'call' => $callModel->getAttributes(),
                        ], 'CallQueueStartCallService::JobsCreate');
                    }
                }

                if (isset($ivrParams['hold_play']) && $ivrParams['hold_play']) {
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
                'message' => 'Sales Communication error: ' . $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine(),
            ];
            $responseData['data']['response'] = $response;
        }
        return $responseData;
    }

    protected function ivrFlowFinish(Call $callModel, DepartmentPhoneProject $departmentPhoneProject): array
    {
        $response = [];

        try {
            $dParams = @json_decode($departmentPhoneProject->dpp_params, true);
            $ivrParams = $dParams['ivr'] ?? [];
            $repeatParams = $dParams['queue_repeat'] ?? [];
            $queueLongTimeParams = new QueueLongTimeNotificationParams(empty($dParams['queue_long_time_notification']) ? [] : $dParams['queue_long_time_notification']);

            $delayJob = 7;
            $job = new CallQueueJob();
            $job->call_id = $callModel->c_id;
            $job->source_id = $departmentPhoneProject->dpp_source_id;
            $job->delay = 0;
            $job->delayJob = $delayJob;
            $jobId = Yii::$app->queue_job->delay($delayJob)->priority(100)->push($job);

            try {
                if (!$jobId) {
                    throw new \DomainException('Not created CallQueueJob');
                }
                if ($repeatParams) {
                    (new RepeatMessageCallJobCreator())->create($callModel, $departmentPhoneProject->dpp_id, $repeatParams);
                }
                if ($queueLongTimeParams->isActive()) {
                    (new QueueLongTimeNotificationJobCreator())->create($callModel, $departmentPhoneProject->dpp_id, $queueLongTimeParams->getDelay() + 7);
                }
            } catch (\Throwable $e) {
                Yii::error([
                    'message' => 'Create call job Error.',
                    'useCase' => 'Processing Incoming call. ivrFlowFinish',
                    'error' => $e->getMessage(),
                    'call' => $callModel->getAttributes(),
                ], 'CallQueueStartCallService::JobsCreate');
            }

            $responseTwml = new VoiceResponse();

            if (!empty($ivrParams['entry_pause'])) {
                $responseTwml->pause(['length' => (int)$ivrParams['entry_pause']]);
            }
            if (!empty($ivrParams['entry_phrase'])) {
                $company = '';
                if ($callModel->cProject && $callModel->cProject->name) {
                    $company = ' ' . strtolower($callModel->cProject->name);
                }
                $entryPhrase = str_replace('{{project}}', $company, $ivrParams['entry_phrase']);
                $sayParams = [];
                if (!empty($ivrParams['entry_language'])) {
                    $sayParams['language'] = $ivrParams['entry_language'];
                }
                if (!empty($ivrParams['entry_voice'])) {
                    $sayParams['voice'] = $ivrParams['entry_voice'];
                }
                $responseTwml->say($entryPhrase, $sayParams);
            }

            if (!empty($ivrParams['hold_play'])) {
                $responseTwml->play($ivrParams['hold_play'], ['loop' => 0]);
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
        } catch (\Throwable $e) {
            $responseTwml = new VoiceResponse();
            $responseTwml->reject(['reason' => 'busy']);
            $response['twml'] = (string) $responseTwml;
            $responseData = [
                'status' => 404,
                'name' => 'Error',
                'code' => 404,
                'message' => 'Sales Communication error: ' . $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine(),
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
            if (!$eq_id) {
                throw new NotFoundHttpException('Not found eq_id', 11);
            }

            if (!$eq_status_id) {
                throw new NotFoundHttpException('Not found eq_status_id', 12);
            }

            $email = Email::findOne(['e_communication_id' => $eq_id]);
            if ($email) {
                if ($eq_status_id > 0) {
                    $email->e_status_id = $eq_status_id;
                    if ($eq_status_id === Email::STATUS_DONE) {
                        $email->e_status_done_dt = date('Y-m-d H:i:s');
                    }

                    if (!$email->save()) {
                        Yii::error(VarDumper::dumpAsString($email->errors), 'API:Communication:updateEmailStatus:Email:save');
                    }
                }

                $response['email'] = $email->e_id;
            } else {
                $response['error'] = 'Not found Communication ID (' . $eq_id . ')';
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
        $response = [];
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

            if (!$sq_id) {
                throw new NotFoundHttpException('Not found sq_id', 11);
            }

            if (!$sq_status_id) {
                throw new NotFoundHttpException('Not found sq_status_id', 12);
            }

            $sid =  $smsParams['sq_tw_message_id'] ?? null;

            $sms = null;

//            if($sid) {
//                $sms = Sms::findOne(['s_tw_message_sid' => $sid]);
//            }

            if (!$sms) {
                $sms = Sms::find()->andWhere(['s_communication_id' => $sq_id])->orderBy(['s_id' => SORT_DESC])->one();
            }


            if ($sms) {
                if ($sq_status_id > 0) {
                    $sms->s_status_id = $sq_status_id;
                    if ($sq_status_id === Sms::STATUS_DONE) {
                        $sms->s_status_done_dt = date('Y-m-d H:i:s');
                    }

                    if ($smsParams) {
                        if (isset($smsParams['sq_tw_price']) && $smsParams['sq_tw_price']) {
                            $sms->s_tw_price = abs((float) $smsParams['sq_tw_price']);
                        }

                        if (isset($smsParams['sq_tw_num_segments']) && $smsParams['sq_tw_num_segments']) {
                            $sms->s_tw_num_segments = (int) $smsParams['sq_tw_num_segments'];
                        }

                        if (isset($smsParams['sq_error_message']) || isset($smsParams['sq_tw_status'])) {
                            $status = !empty($smsParams['sq_tw_status']) ? ('status: ' . $smsParams['sq_tw_status'] . '. ') : '';
                            $sms->s_error_message = $status . ($smsParams['sq_error_message'] ?? '');
                        }

                        if (!$sms->s_tw_message_sid && isset($smsParams['sq_tw_message_id']) && $smsParams['sq_tw_message_id']) {
                            $sms->s_tw_message_sid = $smsParams['sq_tw_message_id'];
                        }
                    }

                    if (!$sms->save()) {
                        Yii::error(VarDumper::dumpAsString($sms->errors), 'API:Communication:updateSmsStatus:Sms:save');
                    } else {
                        $this->sendNotificationUpdateSmsStatus($sms);
                    }
                }
                $response['SmsId'] = $sms->s_id;
            } else {
                $smsModel = SmsDistributionList::find()->where(['sdl_com_id' => $sq_id])->one();

                if ($smsModel) {
                    if ($sq_status_id > 0) {
                        $smsModel->sdl_status_id = $sq_status_id;
//                        if($sq_status_id === SmsDistributionList::STATUS_DONE) {
//                            $sms->s_status_done_dt = date('Y-m-d H:i:s');
//                        }

                        if ($smsParams) {
                            if (!empty($smsParams['sq_tw_price'])) {
                                $smsModel->sdl_price = abs((float)$smsParams['sq_tw_price']);
                            }

                            if (!empty($smsParams['sq_tw_num_segments'])) {
                                $smsModel->sdl_num_segments = (int)$smsParams['sq_tw_num_segments'];
                            }

                            if (isset($smsParams['sq_error_message']) || isset($smsParams['sq_tw_status'])) {
                                $status = !empty($smsParams['sq_tw_status']) ? ('status: ' . $smsParams['sq_tw_status'] . '. ') : '';
                                $smsModel->sdl_error_message = $status . ($smsParams['sq_error_message'] ?? '');
                            }

                            if (!$smsModel->sdl_message_sid && !empty($smsParams['sq_tw_message_id'])) {
                                $smsModel->sdl_message_sid = $smsParams['sq_tw_message_id'];
                            }
                        }

                        if (!$smsModel->save()) {
                            Yii::error(
                                VarDumper::dumpAsString($sms->errors),
                                'API:Communication:updateSmsStatus:SmsDistributionList:save'
                            );
                        }
                    }

                    $response['SmsDistributionId'] = $smsModel->sdl_id;
                } else {
                    $response['error'] = 'Not found SMS or SmsDistributionList ID (' . $sq_id . ')';
                    $response['error_code'] = 13;
                }
            }
        } catch (\Throwable $throwable) {
            Yii::error([
                'message' => $throwable->getMessage(),
            ], 'API:Communication:updateSmsStatus:try');
            $message = $this->debug ? $throwable->getTraceAsString() : AppHelper::throwableFormatter($throwable);
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

            if (!$smsData) {
                throw new NotFoundHttpException('Not found smsData', 11);
            }

            if (!$smsData['sid']) {
                throw new NotFoundHttpException('Not found smsData[sid]', 12);
            }



            $sms = Sms::findOne(['s_tw_message_sid' => $smsData['sid']]);

            if (!$sms) {
                $sms = Sms::find()->andWhere(['s_communication_id' => $comId])->orderBy(['s_id' => SORT_DESC])->one();
            }


            if ($sms) {
                if (isset($smsData['price'])) {
                    $sms->s_tw_price = abs((float) $smsData['price']);
                }

                if (isset($smsData['num_segments']) && $smsData['num_segments']) {
                    $sms->s_tw_num_segments = (int) $smsData['num_segments'];
                }

                if (isset($smsData['sid']) && $smsData['sid']) {
                    if (!$sms->s_tw_message_sid) {
                        $sms->s_tw_message_sid = $smsData['sid'];
                    }
                }

                if (isset($smsData['account_sid']) && $smsData['account_sid']) {
                    if (!$sms->s_tw_account_sid) {
                        $sms->s_tw_account_sid = $smsData['account_sid'];
                    }
                }


                $smsStatusChanged = false;
                if (isset($smsData['status'])) {
                    $sms->s_error_message = 'status: ' . $smsData['status'];

                    if ($smsData['status'] === 'delivered') {
                        $sms->s_status_id = SMS::STATUS_DONE;
                        $smsStatusChanged = true;
                    }
                }

                if (!$sms->save()) {
                    Yii::error(VarDumper::dumpAsString($sms->errors), 'API:Communication:smsFinish:Sms:save');
                } else {
                    if ($smsStatusChanged) {
                        $this->sendNotificationUpdateSmsStatus($sms);
                    }
                }
                $response['sms'] = $sms->attributes;
            } else {
                $smsModel = SmsDistributionList::find()->where(['sdl_com_id' => $comId])->one();

                if ($smsModel) {
                    if (isset($smsData['price'])) {
                        $smsModel->sdl_price = abs((float) $smsData['price']);
                    }

                    if (!empty($smsData['num_segments'])) {
                        $smsModel->sdl_num_segments = (int) $smsData['num_segments'];
                    }

                    if (!empty($smsData['sid']) && !$smsModel->sdl_message_sid) {
                        $smsModel->sdl_message_sid = $smsData['sid'];
                    }

                    if (isset($smsData['status'])) {
                        $smsModel->sdl_error_message = 'status: ' . $smsData['status'];

                        if ($smsData['status'] === 'delivered') {
                            $smsModel->sdl_status_id = SmsDistributionList::STATUS_DONE;
                        }
                    }

                    if (!$smsModel->save()) {
                        Yii::error(VarDumper::dumpAsString($smsModel->errors), 'API:Communication:smsFinish:SmsDistributionList:save');
                    }

                    $response['smsDistribution'] = $smsModel->attributes;
                } else {
                    $response['error'] = 'Not found SMS or Sms Distribution message_sid (' . $smsData['sid'] . ') and not found CommId (' . $comId . ')';
                    $response['error_code'] = 13;
                }
            }
        } catch (\Throwable $throwable) {
            Yii::error($throwable->getTraceAsString(), 'API:Communication:smsFinish:Throwable');
            $message = $this->debug ? $throwable->getTraceAsString() : AppHelper::throwableFormatter($throwable);
            $response['error'] = $message;
            $response['error_code'] = $throwable->getCode();
        }

        return $response;
    }

    /**
     * @return array
     */
    private function getEmailsForReceivedMessages(): array
    {
//        $mailsUpp = UserProjectParams::find()->select(['DISTINCT(upp_email)'])->andWhere(['!=', 'upp_email', ''])->column();
        $mailsUpp = UserProjectParams::find()->select('el_email')->distinct()->joinWith('emailList', false, 'INNER JOIN')->column();
//        $mailsDep = DepartmentEmailProject::find()->select(['DISTINCT(dep_email)'])->andWhere(['!=', 'dep_email', ''])->column();
        $mailsDep = DepartmentEmailProject::find()->select(['el_email'])->distinct()->joinWith('emailList', false, 'INNER JOIN')->column();
        $list = array_merge($mailsUpp, $mailsDep);
        return $list;
    }

    /**
     * @param null $last_id
     * @return array
     */
    private function newEmailMessagesReceived($last_id = null): array
    {
        $response = [];
        try {
            $filter = [];
            $dateTime = null;
            if (null === $last_id) {
                $filter['last_id'] = EmailRepositoryFactory::getRepository()->getLastInboxId() ?? 1;
            } else {
                $filter['last_id'] = (int)$last_id;

                $checkLastEmail = Email::find()->where(['e_inbox_email_id' => $filter['last_id']])->limit(1)->one();
                if ($checkLastEmail) {
                    $response[] = 'Last ID ' . $filter['last_id'] . ' Exists';
                    return $response;
                }

                $filter['last_id'] = EmailRepositoryFactory::getRepository()->getLastInboxId() ?? 1;
            }

            Yii::$app->redis->set('new_email_message_received', true);

//            $filter['limit'] = 20;
//            $filter['mail_list'] = $this->getEmailsForReceivedMessages();

            // push job
            /*
            $job = new ReceiveEmailsJob();
            $job->last_email_id = $filter['last_id'];
            $data = [
                'last_email_id' => $filter['last_id'],
//                'email_list' => $filter['mail_list'],
                'limit' => $filter['limit'],
            ];
            $job->request_data = $data;
            $jobId = \Yii::$app->queue_email_job->push($job);
            */

            $response = [
//                'job_id' => $jobId,
                'last_id' => $filter['last_id'],
            ];

            //Yii::info('JOB (' .VarDumper::dumpAsString($response).') Push ' . VarDumper::dumpAsString($data) . ' last_id: ' . $last_id, 'info\API:newEmailMessagesReceived');
        } catch (\Throwable $e) {
            $message = AppHelper::throwableFormatter($e);
            Yii::error($message, 'API:Communication:newEmailMessagesReceived:Email:try');
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

        if (!\is_array($smsItem)) {
            $response['error'] = 'Sales: Invalid POST request (array)';
            $response['error_code'] = 16;
        }

        if (!isset($smsItem['si_id'])) {
            $response['error'] = 'Sales: Invalid POST request - not found (si_id)';
            $response['error_code'] = 17;
        }

        if (isset($response['error']) && $response['error']) {
            return $response;
        }

        try {
            $form = new SmsIncomingForm();
            $data['SmsIncomingForm'] = $smsItem;
            $form->load($data);
            if ($form->validate()) {
                $response = (Yii::createObject(SmsIncomingService::class))->create($form)->attributes;
            } else {
                Yii::error(VarDumper::dumpAsString($form->errors), 'API:Communication:newSmsMessagesReceived:Sms:validate');
                $response['error_code'] = 12;
                throw new \Exception('Error save SMS data ' . VarDumper::dumpAsString($form->errors));
            }

//                    $sms = new Sms();
//                    $sms->s_type_id = Sms::TYPE_INBOX;
//                    $sms->s_status_id = Sms::STATUS_DONE;
//                    $sms->s_is_new = true;
//
//                    $sms->s_status_done_dt = isset($smsItem['si_sent_dt']) ? date('Y-m-d H:i:s', strtotime($smsItem['si_sent_dt'])) : null;
//
//                    //$sms->s_communication_id = $smsItem['si_id'] ?? null;
//
//                    $sms->s_phone_to = $smsItem['si_phone_to'];
//                    $sms->s_phone_from = $smsItem['si_phone_from'];
//                    $sms->s_project_id = $smsItem['si_project_id'] ?? null;
//                    $sms->s_sms_text = $smsItem['si_sms_text'];
//                    $sms->s_created_dt = $smsItem['si_created_dt'];
//
//                    $sms->s_tw_message_sid = $smsItem['si_message_sid'] ?? null;
//                    $sms->s_tw_num_segments = $smsItem['si_num_segments'] ?? null;
//
//                    $sms->s_tw_to_country = $smsItem['si_to_country'] ?? null;
//                    $sms->s_tw_to_state = $smsItem['si_to_state'] ?? null;
//                    $sms->s_tw_to_city = $smsItem['si_to_city'] ?? null;
//                    $sms->s_tw_to_zip = $smsItem['si_to_zip'] ?? null;
//
//                    $sms->s_tw_from_country = $smsItem['si_from_country'] ?? null;
//                    $sms->s_tw_from_city = $smsItem['si_from_city'] ?? null;
//                    $sms->s_tw_from_state = $smsItem['si_from_state'] ?? null;
//                    $sms->s_tw_from_zip = $smsItem['si_from_zip'] ?? null;
//
//
//                    $lead_id = $sms->detectLeadId();
//
//
//                    if($lead_id) {
//                        $lead = Lead::findOne($lead_id);
//                        if($lead) {
//                            $sms->s_project_id = $lead->project_id;
//                        }
//                        // Yii::info('SMS Detected LeadId '.$lead_id.' from '.$sms->s_phone_from, 'info\API:Communication:newSmsMessagesReceived:Sms');
//                    }
//
//
//                    if(!$sms->save()) {
//                        Yii::error(VarDumper::dumpAsString($sms->errors), 'API:Communication:newSmsMessagesReceived:Sms:save');
//                        $response['error_code'] = 12;
//                        throw new \Exception('Error save SMS data ' . VarDumper::dumpAsString($sms->errors));
//                    }
//
//
//                    //Notifications::create(Yii::$app->user->id, 'Test '.date('H:i:s'), 'Test message <h2>asdasdasd</h2>', Notifications::TYPE_SUCCESS, true);
//
//
//                    $users = $sms->getUsersIdByPhone();
//
//                    $clientPhone = ClientPhone::find()->where(['phone' => $sms->s_phone_from])->orderBy(['id' => SORT_DESC])->limit(1)->one();
//                    if($clientPhone) {
//                        $clientName = $clientPhone->client ? $clientPhone->client->full_name : '-';
//                    } else {
//                        $clientName = '-';
//                    }
//
//                    $user_id = 0;
//
//                    if($users) {
//                        foreach ($users as $user_id) {
//
//                            Notifications::create($user_id, 'New SMS '.$sms->s_phone_from, 'SMS from ' . $sms->s_phone_from .' ('.$clientName.') to '.$sms->s_phone_to.' <br> '.nl2br(Html::encode($sms->s_sms_text))
//                            . ($lead_id ? '<br>Lead ID: '.$lead_id : ''), Notifications::TYPE_INFO, true);
//                            //Notifications::socket($user_id, null, 'getNewNotification', ['sms_id' => $sms->s_id], true);
//
//                            Notifications::sendSocket('getNewNotification', ['user_id' => $user_id], ['sms_id' => $sms->s_id]);
//                        }
//                    }
//
//                    if($user_id > 0) {
//                        $sms->s_created_user_id = $user_id;
//                        $sms->save();
//                    }
//
//                    if($lead_id) {
//                        // Notifications::socket(null, $lead_id, 'updateCommunication', ['sms_id' => $sms->s_id], true);
//                        Notifications::sendSocket('getNewNotification', ['lead_id' => $lead_id], ['sms_id' => $sms->s_id]);
//                    }
//
//                    $response = $sms->attributes;
        } catch (\Throwable $e) {
            Yii::error($e->getTraceAsString(), 'API:Communication:newSmsMessagesReceived:Sms:try');
            $message = $this->debug ? $e->getTraceAsString() : $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
            $response['error'] = $message;
            if (!isset($response['error_code']) || !$response['error_code']) {
                $response['error_code'] = 15;
            }
        }

        return $response;
    }

    /**
     * @param array $response
     * @param bool $enabledLog
     * @return array
     */
    private function getResponseData(array $response, bool $enabledLog = true): array
    {
        if (isset($response['error']) && $response['error']) {
            $responseData = [
                'status'    => 422,
                'name'      => 'Error',
                'code'      => $response['error_code'] ?? 0,
                'message'   => is_string($response['error']) ? $response['error'] : @json_encode($response['error'])
            ];
        } else {
            $responseData = [
                'status'    => 200,
                'name'      => 'Success',
                'code'      => 0,
                'message'   => ''
            ];
        }

        $responseData['data']['response'] = $response;
        if ($enabledLog) {
            $responseData = $this->apiLog->endApiLog($responseData);
        }

        return $responseData;
    }


    /**
     * @param ConferenceRoom $conferenceRoom
     * @param array $postCall
     * @return array
     */
    private function startConference(ConferenceRoom $conferenceRoom, array $postCall): array
    {

        // Yii::info(VarDumper::dumpAsString($postCall), 'info\API:startConference');

        $vr = new VoiceResponse();

        try {
            $call = $this->findOrCreateCallByData($postCall);
            $call->c_source_type_id = Call::SOURCE_CONFERENCE_CALL;
            if (!$call->save()) {
                Yii::error(VarDumper::dumpAsString($call->errors), 'API:CommunicationController:startConference:Call:save');
            }

            $sayParam = ['language' => 'en-US'];   // ['language' => 'en-US', 'voice' => 'alice']

            if ($conferenceRoom->cr_start_dt && strtotime($conferenceRoom->cr_start_dt) > time()) {
                $vr->say('This conference room has not started yet', $sayParam);
                $vr->reject(['reason' => 'busy']);
                Yii::warning('Conference (id: ' . $conferenceRoom->cr_id . ') has not started yet', 'API:CommunicationController:startConference:start');
                return $this->getResponseChownData($vr);
            }

            if ($conferenceRoom->cr_end_dt && strtotime($conferenceRoom->cr_end_dt) < time()) {
                $vr->say('This conference room has already ended', $sayParam);
                $vr->reject(['reason' => 'busy']);
                Yii::warning('Conference (id: ' . $conferenceRoom->cr_id . ') has already ended', 'API:CommunicationController:startConference:end');
                return $this->getResponseChownData($vr);
            }

            $vr->pause(['length' => 3]);
            if ($conferenceRoom->cr_welcome_message) {
                $vr->say($conferenceRoom->cr_welcome_message, $sayParam);
            }

            if ($conferenceRoom->cr_moderator_phone_number && $conferenceRoom->cr_moderator_phone_number === $call->c_from) {
                $vr->pause(['length' => 1]);
                $vr->say('You are the moderator of this conference.', $sayParam);
                $conferenceRoom->cr_param_start_conference_on_enter = true;
                $conferenceRoom->cr_param_end_conference_on_exit = true;
                $conferenceRoom->cr_param_muted = false;
            } else {
                $conferenceRoom->cr_param_start_conference_on_enter = false;
                $conferenceRoom->cr_param_end_conference_on_exit = false;
            }

            // $vr->redirect('/v1/twilio/voice-gather/?step=1', ['method' => 'POST']);

            $dial = $vr->dial('');
            $params = $conferenceRoom->getCreatedTwParams();

            //$vr->pause(['length' => 3]);
            $dial->conference($conferenceRoom->cr_key, $params);

            /*$conference = new Conference();
            $conference->cf_cr_id = $conferenceRoom->cr_id;
            $conference->cf_options = @json_encode($conferenceRoom->attributes);
            $conference->cf_status_id = Conference::STATUS_START;
            if (!$conference->save()) {
                Yii::error(VarDumper::dumpAsString($conference->errors), 'API:CommunicationController:startConference:Conference:save');
            }*/
        } catch (\Throwable $e) {
            $vr->say('Conference Error!');
            $vr->reject(['reason' => 'busy']);
            return $this->getResponseChownData($vr, 404, 404, 'Sales Communication error: ' . $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine());
        }

        return $this->getResponseChownData($vr);
    }

    private function voiceConferenceCallCallback(array $post = []): array
    {
        $response = [];

        if (empty($post['conferenceData'])) {
            $response['error'] = 'Not found POST[conferenceData]';
            Yii::error($response['error'] . ' - ' . VarDumper::dumpAsString($post), 'API:Communication:voiceConferenceCallCallback:emptyConferenceData');
            return $response;
        }

        $form = new ConferenceStatusCallbackForm();

        if (!$form->load($post['conferenceData'])) {
            $response['error'] = 'POST[conferenceData] cant load data';
            Yii::error(VarDumper::dumpAsString([
                'message' => $response['error'],
                'post' => $post,
            ]), 'API:Communication:voiceConferenceCallCallback:cantLoadData');
            return $response;
        }

        if (!$form->validate()) {
            $response['error'] = 'POST[conferenceData] validate error';
            Yii::error(VarDumper::dumpAsString([
                'message' => $response['error'],
                'errors' => $form->getErrors(),
                'form' => $form->getAttributes(),
                'post' => $post,
            ]), 'API:Communication:voiceConferenceCallCallback:validate');
            return $response;
        }

        $conference = Conference::findOne(['cf_sid' => $form->ConferenceSid]);

        if (!$conference) {
            $conference = new Conference();
            $conference->start();
            $conference->cf_sid = $form->ConferenceSid;
            $conference->cf_friendly_name = $form->friendly_name;
            $conference->cf_call_sid = $form->CallSid;
            $conference->cf_created_user_id = $form->conference_created_user_id;
            $conference->cf_recording_disabled = $form->call_recording_disabled;

            try {
                if (!$conference->save()) {
                    $errors = $conference->getErrors();
                    if (
                        count($errors) === 1
                        && array_key_exists('cf_sid', $errors)
                        && count($errors['cf_sid']) === 1
                        && strpos($errors['cf_sid'][0], 'has already been taken') !== false
                    ) {
                        // 2 callback received in one time
                    } else {
                        Yii::error(VarDumper::dumpAsString([
                            'errors' => $errors,
                            'model' => $conference->getAttributes(),
                            'post' => $post,
                            'form' => $form->getAttributes(),
                        ]), 'API:Communication:voiceConferenceCallCallback:save');
                    }
                    $conference = Conference::findOne(['cf_sid' => $form->ConferenceSid]);
                }
            } catch (\Throwable $e) {
                $isDuplicateError = DuplicateExceptionChecker::isDuplicate($e->getMessage());
                if ($isDuplicateError) {
                    Yii::info(
                        array_merge(
                            [
                                'msg' => 'Some conference callback received in one time',
                                'post' => $post,
                                'form' => $form->getAttributes(),
                            ],
                            AppHelper::throwableLog($e, false),
                        ),
                        'log\API:CommunicationController:voiceConferenceCallCallback'
                    );
                } else {
                    Yii::error(
                        array_merge(
                            [
                                'post' => $post,
                                'form' => $form->getAttributes(),
                            ],
                            AppHelper::throwableLog($e, false),
                        ),
                        'API:CommunicationController:voiceConferenceCallCallback'
                    );
                }
                $conference = Conference::findOne(['cf_sid' => $form->ConferenceSid]);
            }
        }

        if (
            $conference
            && !$conference->cf_call_sid
            && $form->CallSid
            && ($form->StatusCallbackEvent === Conference::EVENT_PARTICIPANT_JOIN || $form->StatusCallbackEvent === Conference::EVENT_PARTICIPANT_LEAVE)
        ) {
            $conference->cf_call_sid = $form->CallSid;
            if (!$conference->save()) {
                Yii::error(VarDumper::dumpAsString([
                    'errors' => $conference->getErrors(),
                    'model' => $conference->getAttributes(),
                    'post' => $post,
                    'form' => $form->getAttributes(),
                ]), 'API:Communication:voiceConferenceCallCallback:SecondTry');
            }
        }

        if (
            ($form->StatusCallbackEvent === Conference::EVENT_PARTICIPANT_JOIN || $form->StatusCallbackEvent === Conference::EVENT_PARTICIPANT_LEAVE)
            && $form->is_warm_transfer
        ) {
            $call = Call::find()->andWhere(['c_call_sid' => $form->CallSid])->one();
            if ($call && !$call->isStatusQueue()) {
                if ($form->old_call_owner_id && $form->call_group_id) {
                    UserStatus::updateIsOnnCall($form->old_call_owner_id, $form->call_group_id);
                }
                $call->c_created_user_id = $form->accepted_user_id;
                $call->setTypeIn();
                $call->direct();
                if ($form->dep_id) {
                    $call->c_dep_id = $form->dep_id;
                }
                $call->save();
            }
        }

        if (!$conference) {
            Yii::error(VarDumper::dumpAsString([
                'post' => $post,
                'form' => $form->getAttributes(),
            ]), 'API:Communication:voiceConferenceCallCallback:Not:SavedFound');
            $response['error'] = 'Not found and not saved Conference';
            return $response;
        }

        $form->conferenceId = $conference->cf_id;

        if ($form->StatusCallbackEvent === Conference::EVENT_CONFERENCE_START) {
            $this->conferenceStatusCallbackHandler->start($conference, $form);
        } elseif ($form->StatusCallbackEvent === Conference::EVENT_CONFERENCE_END) {
            $this->conferenceStatusCallbackHandler->end($conference, $form);
        } elseif ($form->StatusCallbackEvent === Conference::EVENT_PARTICIPANT_JOIN) {
            $this->conferenceStatusCallbackHandler->join($conference, $form);
        } elseif ($form->StatusCallbackEvent === Conference::EVENT_PARTICIPANT_HOLD) {
            $this->conferenceStatusCallbackHandler->hold($conference, $form);
        } elseif ($form->StatusCallbackEvent === Conference::EVENT_PARTICIPANT_UNHOLD) {
            $this->conferenceStatusCallbackHandler->unHold($conference, $form);
        } elseif ($form->StatusCallbackEvent === Conference::EVENT_PARTICIPANT_LEAVE) {
            $this->conferenceStatusCallbackHandler->leave($conference, $form);
        } elseif ($form->StatusCallbackEvent === Conference::EVENT_PARTICIPANT_MUTE) {
            $this->conferenceStatusCallbackHandler->mute($conference, $form);
        } elseif ($form->StatusCallbackEvent === Conference::EVENT_PARTICIPANT_UNMUTE) {
            $this->conferenceStatusCallbackHandler->unMute($conference, $form);
        }

        return $response;
    }

    private function voiceConferenceCallRecordCallback(array $post = []): array
    {
        $response = [];

        if (empty($post['conferenceData'])) {
            $response['error'] = 'Not found POST[conferenceData]';
            Yii::error($response['error'] . ' - ' . VarDumper::dumpAsString($post), 'API:Communication:voiceConferenceCallRecordCallback');
            return $response;
        }

        $form = new ConferenceRecordingStatusCallbackForm();

        if (!$form->load($post['conferenceData'])) {
            $response['error'] = 'POST[conferenceData] cant load data';
            Yii::error(VarDumper::dumpAsString([
                'message' => $response['error'],
                'post' => $post,
            ]), 'API:Communication:voiceConferenceCallRecordCallback');
            return $response;
        }

        if (!$form->validate()) {
            $response['error'] = 'POST[conferenceData] validate error';
            Yii::error(VarDumper::dumpAsString([
                'message' => $response['error'],
                'errors' => $form->getErrors(),
                'form' => $form->getAttributes(),
                'post' => $post,
            ]), 'API:Communication:voiceConferenceCallRecordCallback');
            return $response;
        }

        if (!$conference = Conference::findOne(['cf_sid' => $form->ConferenceSid])) {
            $response['error'] = 'Not found Conference SID ' . $form->ConferenceSid;
            Yii::error(VarDumper::dumpAsString([
                'message' => $response['error'],
                'form' => $form->getAttributes(),
                'post' => $post,
            ]), 'API:Communication:voiceConferenceCallRecordCallback');
            return $response;
        }

        if ($form->RecordingUrl) {
            $conference->cf_recording_url = $form->RecordingUrl;
        }
        if ($form->RecordingDuration) {
            $conference->cf_recording_duration = $form->RecordingDuration;
        }
        if ($form->RecordingSid) {
            $conference->cf_recording_sid = $form->RecordingSid;
        }
        $conference->cf_updated_dt = date('Y-m-d H:i:s');

        if ($conference->save()) {
            $response['conference'] = $conference->getAttributes();
//            $service = \Yii::createObject(CallLogConferenceTransferService::class);
//            $service->saveRecord($conference->cf_id, $form);
        } else {
            Yii::error(VarDumper::dumpAsString([
                'errors' => $conference->getErrors(),
                'model' => $conference->getAttributes(),
                'post' => $post,
            ]), 'API:Communication:voiceConferenceCallRecordCallback');
            $response['error'] = VarDumper::dumpAsString($conference->getErrors());
        }

        return $response;
    }

    private function voiceConferenceCallback(array $post = []): array
    {
        $response = [];

        // Yii::info(VarDumper::dumpAsString($post), 'info\API:Communication:voiceConferenceCallback');

        //$agentId = null;

        if (isset($post['conferenceData']['ConferenceSid']) && $post['conferenceData']['ConferenceSid']) {
            $conferenceData = $post['conferenceData'];
            $conferenceSid = mb_substr($conferenceData['ConferenceSid'], 0, 34);

            $conference = Conference::findOne(['cf_sid' => $conferenceSid]);

            if (!$conference) {
                $without_room_id = $conferenceData['without_room_id'] ?? false;

                if ($without_room_id) {
                    $conference = new Conference();
                    $conference->cf_status_id = Conference::STATUS_START;
                    $conference->cf_sid = $conferenceSid;
                    $conference->cf_friendly_name = $conferenceData['friendly_name'] ?? null;
                    $conference->cf_call_sid = $conferenceData['friendly_name'] ?? null;

                    try {
                        if (!$conference->save()) {
                            Yii::error(
                                VarDumper::dumpAsString($conference->errors),
                                'API:CommunicationController:startConference:Conference:without_room_id:save'
                            );
                        }
                    } catch (\Throwable $e) {
                        $conference = Conference::findOne(['cf_sid' => $conferenceSid]);
                        Yii::error($e->getMessage(), 'API:CommunicationController:startConference:Conference:without_room_id:throwable:save');
                    }
                } else {
                    $conferenceRoom = ConferenceRoom::find()->where(['cr_key' => $conferenceData['FriendlyName'], 'cr_enabled' => true])->limit(1)->one();

                    if ($conferenceRoom) {
                        $conference = new Conference();
                        $conference->cf_cr_id = $conferenceRoom->cr_id;
                        $conference->cf_options = @json_encode($conferenceRoom->attributes);
                        $conference->cf_status_id = Conference::STATUS_START;
                        $conference->cf_sid = $conferenceSid;
                        try {
                            if (!$conference->save()) {
                                Yii::error(
                                    VarDumper::dumpAsString($conference->errors),
                                    'API:CommunicationController:startConference:Conference:save'
                                );
                            }
                        } catch (\Throwable $e) {
                            $conference = Conference::findOne(['cf_sid' => $conferenceSid]);
                            Yii::error($e->getMessage(), 'API:CommunicationController:startConference:Conference:with_room_id:throwable:save');
                        }
                    } else {
                        Yii::warning(
                            'Not found ConferenceRoom by key: conferenceData - ' . VarDumper::dumpAsString($conferenceData),
                            'API:CommunicationController:startConference:conferenceData:notfound'
                        );
                    }
                }
            }

            if ($conference) {
                /*
                 *  conference-end
                    conference-start
                    participant-leave
                    participant-join
                    participant-mute
                    participant-unmute
                    participant-hold
                    participant-unhold
                    participant-speech-start
                    participant-speech-stop
                 */


                //$cf->cf_sid
                if ($conferenceData['StatusCallbackEvent'] === 'conference-end') {
                    $conference->cf_status_id = Conference::STATUS_END;
                    if (!$conference->save()) {
                        Yii::error(
                            VarDumper::dumpAsString($conference->errors),
                            'API:CommunicationController:startConference:Conference:save-end'
                        );
                    }
                } elseif ($conferenceData['StatusCallbackEvent'] === 'participant-join') {
                    $call = Call::find()->where(['c_call_sid' => $conferenceData['CallSid']])->one();
                    if ($call && $call->c_conference_sid !== $conference->cf_sid) {
                        $call->c_conference_sid = $conference->cf_sid;
                        if (!$call->save()) {
                            Yii::error(
                                VarDumper::dumpAsString($call->errors),
                                'API:CommunicationController:voiceConferenceCallback:participant-join:call:save'
                            );
                        }
                    }

                    $cPart = new ConferenceParticipant();
                    $cPart->cp_cf_id = $conference->cf_id;
                    $cPart->cp_call_sid = $conferenceData['CallSid'];
                    if ($call) {
                        $cPart->cp_call_id = $call->c_id;
                    }
                    $cPart->join();
                    $cPart->cp_join_dt = date('Y-m-d H:i:s');
                    if (!$cPart->save()) {
                        Yii::error(VarDumper::dumpAsString($cPart->errors), 'API:Communication:voiceConferenceCallback:ConferenceParticipant:save-join');
                    }

                    // $conference->cf_status_id = Conference::STATUS_START;
                } elseif ($conferenceData['StatusCallbackEvent'] === 'participant-hold') {
                    $cPart = ConferenceParticipant::find()->where([
                        'cp_cf_id' => $conference->cf_id,
                        'cp_call_sid' => $conferenceData['CallSid'],

                    ])->one();

                    if ($cPart) {
                        $cPart->hold(date('Y-m-d H:i:s'));
                        if (!$cPart->save()) {
                            Yii::error(VarDumper::dumpAsString($cPart->errors), 'API:Communication:voiceConferenceCallback:ConferenceParticipant:save-hold');
                        }
                    } else {
                        $call = Call::find()->where(['c_call_sid' => $conferenceData['CallSid']])->one();
                        if ($call && $call->c_conference_sid !== $conference->cf_sid) {
                            $call->c_conference_sid = $conference->cf_sid;
                            if (!$call->save()) {
                                Yii::error(
                                    VarDumper::dumpAsString($call->errors),
                                    'API:CommunicationController:voiceConferenceCallback:participant-hold:call:save'
                                );
                            }
                        }

                        $cPart = new ConferenceParticipant();
                        $cPart->cp_cf_id = $conference->cf_id;
                        $cPart->cp_call_sid = $conferenceData['CallSid'];
                        $cPart->hold(date('Y-m-d H:i:s'));
                        if ($call) {
                            $cPart->cp_call_id = $call->c_id;
                        }
                        if (!$cPart->save()) {
                            Yii::error(VarDumper::dumpAsString($cPart->errors), 'API:Communication:voiceConferenceCallback:ConferenceParticipant:save-hold');
                        }
                    }
                } elseif ($conferenceData['StatusCallbackEvent'] === 'participant-unhold') {
                    $cPart = ConferenceParticipant::find()->where([
                        'cp_cf_id' => $conference->cf_id,
                        'cp_call_sid' => $conferenceData['CallSid'],

                    ])->one();

                    if ($cPart) {
                        $cPart->join();
                        if (!$cPart->save()) {
                            Yii::error(VarDumper::dumpAsString($cPart->errors), 'API:Communication:voiceConferenceCallback:ConferenceParticipant:save-hold');
                        }
                    } else {
                        $call = Call::find()->where(['c_call_sid' => $conferenceData['CallSid']])->one();
                        if ($call && $call->c_conference_sid !== $conference->cf_sid) {
                            $call->c_conference_sid = $conference->cf_sid;
                            if (!$call->save()) {
                                Yii::error(
                                    VarDumper::dumpAsString($call->errors),
                                    'API:CommunicationController:voiceConferenceCallback:participant-hold:call:save'
                                );
                            }
                        }

                        $cPart = new ConferenceParticipant();
                        $cPart->cp_cf_id = $conference->cf_id;
                        $cPart->cp_call_sid = $conferenceData['CallSid'];
                        $cPart->join();
                        if ($call) {
                            $cPart->cp_call_id = $call->c_id;
                        }
                        if (!$cPart->save()) {
                            Yii::error(VarDumper::dumpAsString($cPart->errors), 'API:Communication:voiceConferenceCallback:ConferenceParticipant:save-hold');
                        }
                    }
                } elseif ($conferenceData['StatusCallbackEvent'] === 'participant-leave') {
                    //$conference->cf_status_id = Conference::STATUS_START;

                    $cPart = ConferenceParticipant::find()->where([
                        'cp_cf_id' => $conference->cf_id,
                        'cp_call_sid' => $conferenceData['CallSid'],

                    ])->one();

                    if ($cPart) {
                        $cPart->leave(date('Y-m-d H:i:s'));
                        if (!$cPart->save()) {
                            Yii::error(
                                VarDumper::dumpAsString($cPart->errors),
                                'API:Communication:voiceConferenceCallback:ConferenceParticipant:save-leave'
                            );
                        }
                    } else {
                        $call = Call::find()->where(['c_call_sid' => $conferenceData['CallSid']])->one();
                        if ($call && $call->c_conference_sid !== $conference->cf_sid) {
                            $call->c_conference_sid = $conference->cf_sid;
                            if (!$call->save()) {
                                Yii::error(
                                    VarDumper::dumpAsString($call->errors),
                                    'API:CommunicationController:voiceConferenceCallback:participant-leave:call:save'
                                );
                            }
                        }

                        $cPart = new ConferenceParticipant();
                        $cPart->cp_cf_id = $conference->cf_id;
                        $cPart->cp_call_sid = $conferenceData['CallSid'];
                        $cPart->leave(date('Y-m-d H:i:s'));
                        if ($call) {
                            $cPart->cp_call_id = $call->c_id;
                        }
                        if (!$cPart->save()) {
                            Yii::error(VarDumper::dumpAsString($cPart->errors), 'API:Communication:voiceConferenceCallback:ConferenceParticipant:save-leave');
                        }
                    }
                }
            }

//            if(!$call->save()) {
//                Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceDefault:Call2:save');
//            }
        } else {
            //Yii::error('Not found POST[callData][CallSid] ' . VarDumper::dumpAsString($post), 'API:Communication:voiceDefault:callData:notFound');
            $response['error'] = 'Not found POST[conferenceData][ConferenceSid]';
            Yii::error($response['error'] . ' - ' . VarDumper::dumpAsString($post), 'API:Communication:voiceConferenceCallback:notFound');
        }


        return $response;
    }

    private function voiceConferenceRecordCallback(array $post = []): array
    {
        $response = [];

        // Yii::info(VarDumper::dumpAsString($post), 'info\API:Communication:voiceConferenceRecordCallback');

        //$agentId = null;

        if (isset($post['conferenceData']['ConferenceSid']) && $post['conferenceData']['ConferenceSid']) {
            $conferenceData = $post['conferenceData'];
            $conferenceSid = mb_substr($conferenceData['ConferenceSid'], 0, 34);


            $conference = Conference::findOne(['cf_sid' => $conferenceSid]);


            // $callSid = $conferenceData['CallSid'] ?? null;
            $conferenceSid = $conferenceData['ConferenceSid'] ?? null;
            $recordingSid = $conferenceData['RecordingSid'] ?? null;
            $recordingUrl = $conferenceData['RecordingUrl'] ?? null;
            $recordingDuration = $conferenceData['RecordingDuration'] ?? null;


            if ($conference) {
                if ($recordingUrl) {
                    $conference->cf_recording_url = $recordingUrl;
                }

                if ($recordingDuration) {
                    $conference->cf_recording_duration = $recordingDuration;
                }

                if ($recordingSid) {
                    $conference->cf_recording_sid = $recordingSid;
                }

                $conference->cf_updated_dt = date('Y-m-d H:i:s');
                if ($conference->save()) {
                    $response['conference'] = $conference->attributes;
                } else {
                    Yii::error(VarDumper::dumpAsString($conference->errors), 'API:TwilioController:actionConferenceRecordingStatusCallback:Conference:update');
                    $response['error'] = VarDumper::dumpAsString($conference->errors);
                }
            } else {
                $response['error'] = 'Not found Conference SID: ' . $conferenceSid;
                Yii::error($response['error'] . ' - ' . VarDumper::dumpAsString($post), 'API:Communication:voiceConferenceRecordCallback:notFound');
            }

//            if(!$call->save()) {
//                Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceDefault:Call2:save');
//            }
        } else {
            //Yii::error('Not found POST[callData][CallSid] ' . VarDumper::dumpAsString($post), 'API:Communication:voiceDefault:callData:notFound');
            $response['error'] = 'Not found POST[conferenceData][ConferenceSid]';
            Yii::error($response['error'] . ' - ' . VarDumper::dumpAsString($post), 'API:Communication:voiceConferenceRecordCallback:notFoundData');
        }

        return $response;
    }

    /**
     * @param VoiceResponse $vr
     * @param int $status
     * @param int $code
     * @param string $message
     * @return array
     */
    private function getResponseChownData(VoiceResponse $vr, int $status = 200, int $code = 0, string $message = ''): array
    {
        $response['twml'] = (string) $vr;
        $responseData = [
            'status' => $status,
            'name' => ($status === 200 ? 'Success' : 'Error'),
            'code' => $code,
            'message' => $message,
            'data' => ['response' => $response]
        ];

        return $responseData;
    }

    private function sendNotificationUpdateSmsStatus(Sms $sms): void
    {
        $usersForNotify = [];
        $repo = Yii::createObject(UserProjectParamsRepository::class);
        if ($sms->isOut()) {
            $usersForNotify = $repo->findUsersIdByPhone($sms->s_phone_from);
        } elseif ($sms->isIn()) {
            $usersForNotify = $repo->findUsersIdByPhone($sms->s_phone_to);
        }
        foreach ($usersForNotify as $userForNotify) {
            Notifications::publish('phoneWidgetSmsSocketMessage', ['user_id' => $userForNotify], Message::updateStatus($sms));
        }
    }

    private function returnTwmlAsBusy(?string $message): VoiceResponse
    {
        $vr = new VoiceResponse();
        if ($message) {
            $vr->pause(['length' => 5]);
            $vr->say($message, [
                'language' => 'en-US',
                'voice' => 'alice'
            ]);
        }
        $vr->reject(['reason' => 'busy']);
        return $vr;
    }

    private function saveLogExecutionTimeToCallJson(Call $model, array $logExecutionTimeResult): void
    {
        $data = JsonHelper::decode($model->c_data_json);
        $data['logExecutionTime'] = $logExecutionTimeResult;
        $model->c_data_json = JsonHelper::encode($data);
    }

    public function actionCallJoinedToWrongConference($post)
    {
        $response = new VoiceResponse();
        $response->say('Server error.');

        return $this->getResponseChownData($response);
    }
}
