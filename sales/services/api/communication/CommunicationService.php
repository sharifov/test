<?php

namespace sales\services\api\communication;

use common\models\Call;
use common\models\CallSession;
use common\models\ClientPhone;
use common\models\Employee;
use common\models\Lead;
use common\models\Lead2;
use common\models\Notifications;
use common\models\Project;
use common\models\ProjectEmployeeAccess;
use common\models\Sources;
use common\models\UserProfile;
use common\models\UserProjectParams;
use sales\forms\api\communication\voice\client\ClientForm;
use sales\forms\api\communication\voice\defaults\DefaultsForm;
use sales\forms\api\communication\voice\finish\FinishForm;
use sales\forms\api\communication\voice\incoming\IncomingForm;
use sales\forms\api\communication\voice\record\RecordForm;
use sales\repositories\call\CallRepository;
use sales\repositories\call\CallSessionRepository;
use sales\repositories\client\ClientPhoneRepository;
use sales\repositories\client\ClientRepository;
use sales\repositories\lead\LeadRepository;
use sales\repositories\NotFoundException;
use sales\repositories\source\SourceRepository;
use sales\repositories\user\UserProjectParamsRepository;
use sales\settings\CommunicationSettings;
use Twilio\TwiML\VoiceResponse;
use Yii;
use yii\helpers\VarDumper;

/**
 * Class CommunicationService
 * @property CallRepository $callRepository
 * @property UserProjectParamsRepository $userProjectParamsRepository
 * @property LeadRepository $leadRepository
 * @property ClientPhoneRepository $clientPhoneRepository
 * @property CallSessionRepository $callSessionRepository
 * @property SourceRepository $sourceRepository
 */
class CommunicationService
{

    private $callRepository;
    private $userProjectParamsRepository;
    private $leadRepository;
    private $clientPhoneRepository;
    private $callSessionRepository;
    private $sourceRepository;

    /**
     * CommunicationService constructor.
     * @param CallRepository $callRepository
     * @param UserProjectParamsRepository $userProjectParamsRepository
     * @param LeadRepository $leadRepository
     * @param ClientPhoneRepository $clientPhoneRepository
     * @param CallSessionRepository $callSessionRepository
     * @param SourceRepository $sourceRepository
     */
    public function __construct(
        CallRepository $callRepository,
        UserProjectParamsRepository $userProjectParamsRepository,
        LeadRepository $leadRepository,
        ClientPhoneRepository $clientPhoneRepository,
        CallSessionRepository $callSessionRepository,
        SourceRepository $sourceRepository
    )
    {
        $this->callRepository = $callRepository;
        $this->userProjectParamsRepository = $userProjectParamsRepository;
        $this->leadRepository = $leadRepository;
        $this->clientPhoneRepository = $clientPhoneRepository;
        $this->callSessionRepository = $callSessionRepository;
        $this->sourceRepository= $sourceRepository;
    }

    /**
     * @param array $post
     * @return array
     */
    private static function prepareDataVoiceDefault(array $post): array
    {
        $data = [];
        if (isset($post['callData'])) {
            $data['CallDataForm'] = $post['callData'];
            unset($post['callData']);
        }
        if (isset($post['call'])) {
            $data['CallForm'] = $post['call'];
            unset($post['call']);
        }
        $data['DefaultsForm'] = $post;
        return $data;
    }

    /**
     * @param array $post
     * @return array
     */
    public function voiceDefault(array $post): array
    {
        Yii::info(VarDumper::dumpAsString($post), 'info\API:Communication:voiceDefault');

        $response = ['trace' => ''];
        $trace = [];

        $agentId = null;

        $originalPost = $post;
        $post = self::prepareDataVoiceDefault($post);

        $defaultForm = new DefaultsForm();

        if (!$defaultForm->load($post)) {
            Yii::error('Can not load post data: POST: ' . VarDumper::dumpAsString($originalPost), 'API:CommunicationService:voiceDefault:defaultForm:load');
            return $response;
        }

        if (!$defaultForm->validate()) {
            Yii::error(VarDumper::dumpAsString($defaultForm->errors), 'API:CommunicationService:voiceDefault:defaultForm:validate');
            return $response;
        }

        if ($defaultForm->callData->isEmptyCallSid()) {
            Yii::error('Communication Request: Not found post[callData][sid] ' . VarDumper::dumpAsString($originalPost), 'API:CommunicationService:voiceFinish:empty:CallSid');
            return $response;
        }

        if (isset($post['callData']['Called']) && $post['callData']['Called']) {

            //$trace[] = 'pos' . strpos($post['callData']['Called'], 'client:seller');

            if (strpos($post['callData']['Called'], 'client:seller') !== false) {
                $agentId = (int)str_replace('client:seller', '', $post['callData']['Called']);
                $trace[] = 'find 1788 $agentId:' . $agentId;
            } else {
                // if cancel call in first seconds
                if (!isset($post['callData']['ParentCallSid']) && isset($post['callData']['CallStatus']) && in_array($post['callData']['CallStatus'], [Call::CALL_STATUS_CANCELED])) {
                    $callsIfCancel = Call::findAll(['c_call_sid' => $post['callData']['CallSid']]);
                    if ($callsIfCancel) {
                        foreach ($callsIfCancel AS $cancelCall) {
                            $cancelCall->c_call_status = $post['callData']['CallStatus'];
                            $cancelCall->save();
                        }
                    }
                    $trace[] = 'if cancel call in first seconds:status' . $post['callData']['CallStatus'];
                }
            }
        }
        $call = null;

        if (!$agentId) {
            if (isset($post['callData']['c_user_id']) && $post['callData']['c_user_id']) {
                $agentId = $post['callData']['c_user_id'];
                $trace[] = 'agent id (1810): ' . $agentId;
            }
        }

        if ($agentId) {
            $call = Call::find()->where(['c_call_sid' => $post['callData']['CallSid']])->andWhere(['c_created_user_id' => $agentId])->limit(1)->one();
            //$trace[] = 'call 1812' .  ($call && $call->c_id) ? $call->c_id : 0;
        } else {
            if (isset($post['call'], $post['call']['c_call_type_id']) && $post['call']['c_call_type_id'] && (int)$post['call']['c_call_type_id'] === Call::CALL_TYPE_OUT) {
                $call = Call::find()->where(['c_call_sid' => $post['callData']['CallSid']])->limit(1)->one();
                //$trace[] = 'call 1818' .  ($call && $call->c_id) ? $call->c_id : 0;
            }
        }

        //$trace[] = 'call 1823' . ($call && $call->c_id) ? $call->c_id : 0;

        if (isset($post['callData']['ParentCallSid']) && $post['callData']['ParentCallSid']) {
            $childCall = true;
        } else {
            $childCall = false;
        }


        if (!$call) {
            $call = Call::find()->where(['c_call_sid' => $post['callData']['CallSid'], 'c_created_user_id' => null])->one();
            if ($call && $agentId) {
                $call->c_created_user_id = $agentId;
                //$call->save();
            }
        }


        if ($childCall) {
            if (!$call) {
                $call = Call::find()->where(['c_call_sid' => $post['callData']['ParentCallSid'], 'c_created_user_id' => $agentId])->one();
            }

            if (!$call) {
                $call = Call::find()->where(['c_call_sid' => $post['callData']['ParentCallSid'], 'c_created_user_id' => null])->one();
                if ($call && $agentId) {
                    $call->c_created_user_id = $agentId;
                    //$call->save();
                }
            }
        }


        if ($call) {

            if (isset($post['callData']['CallStatus']) && $post['callData']['CallStatus']) {
                if ($call->c_call_status && !in_array($call->c_call_status, [Call::CALL_STATUS_NO_ANSWER, Call::CALL_STATUS_BUSY, Call::CALL_STATUS_COMPLETED, Call::CALL_STATUS_CANCELED])) {
                    $call->c_call_status = $post['callData']['CallStatus'];
                }

                if (isset($post['call']) && $post['call']) {
                    if (isset($post['call']['c_call_duration']) && $post['call']['c_call_duration']) {
                        $call->c_call_duration = (int)$post['call']['c_call_duration'];
                    }
                } else {
                    $call->c_call_duration = 1;
                }
                if (!$call->save()) {
                    \Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceDefault:Call:save');
                }
                Notifications::socket($call->c_created_user_id, $call->c_lead_id, 'webCallUpdate', ['status' => $call->c_call_status, 'duration' => $call->c_call_duration, 'debug' => 'TYPE_VOIP'], true);
            }


            $call_status = $post['callData']['CallStatus'];
            $otherCalls = Call::find()->where(['c_call_sid' => $call->c_call_sid])->andWhere(['<>', 'c_id', $call->c_id])->all();
            $trace[] = '$otherCalls: ' . count($otherCalls);
            $otherCallArr = [];

            if ($otherCalls && $call_status === Call::CALL_STATUS_IN_PROGRESS) {
                foreach ($otherCalls as $otherCall) {
                    $otherCallArr[] = $otherCall->attributes;

                    if ($otherCall->c_call_status === Call::CALL_STATUS_RINGING) {
                        $otherCall->c_call_status = Call::CALL_STATUS_CANCELED;
                        //$otherCall->c_call_status = Call::CALL_STATUS_NO_ANSWER;
                        $otherCall->c_updated_dt = date('Y-m-d H:i:s');

                        if (!$otherCall->save()) {
                            Yii::error('Call ID: ' . $otherCall->c_id . ' ' . VarDumper::dumpAsString($otherCall->errors), 'API:Communication:voiceDefault:otherCall:save');
                        }
                    }
                }
            }

            //Yii::info($call->c_call_sid . ' ' . VarDumper::dumpAsString($call->attributes) . ' Other Calls: ' . VarDumper::dumpAsString($otherCallArr), 'info\API:Voice:VOIP:CallBack');


            if ($call->c_call_status === Call::CALL_STATUS_NO_ANSWER || $call->c_call_status === Call::CALL_STATUS_BUSY || $call->c_call_status === Call::CALL_STATUS_CANCELED || $call->c_call_status === Call::CALL_STATUS_FAILED) {

                if ($call->c_lead_id) {
                    $lead = $call->cLead;
                    $lead->l_call_status_id = Lead::CALL_STATUS_CANCEL;
                    if (!$lead->save()) {
                        Yii::error('lead: ' . $lead->id . ' ' . VarDumper::dumpAsString($lead->errors), 'API:Communication:voiceDefault:Lead:save');
                    }
                }

            } else {

                if (isset($post['callData']['CallStatus']) && $post['callData']['CallStatus']) {
                    $call->c_call_status = $post['callData']['CallStatus'];
                }

            }

            if (!$childCall) {
                $call->c_sequence_number = (int) $post['callData']['SequenceNumber'] ?? 0;

                if (isset($post['callData']['CallDuration'])) {
                    $call->c_call_duration = (int)$post['callData']['CallDuration'];
                }

                if (isset($post['call']['c_tw_price']) && $post['call']['c_tw_price']) {
                    $call->c_price = abs((float)$post['call']['c_tw_price']);
                }
            }

            $call->c_updated_dt = date('Y-m-d H:i:s');
            if (!$call->save()) {
                Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceDefault:Call2:save');
            }
            if ($call->c_lead_id) {
                //Notifications::create($user_id, 'New SMS '.$sms->s_phone_from, 'SMS from ' . $sms->s_phone_from .' ('.$clientName.') to '.$sms->s_phone_to.' <br> '.nl2br(Html::encode($sms->s_sms_text))
                // . ($lead_id ? '<br>Lead ID: '.$lead_id : ''), Notifications::TYPE_INFO, true);
                //Notifications::socket(null, $call->c_lead_id, 'callUpdate', ['status' => $call->c_call_status, 'duration' => $call->c_call_duration, 'snr' => $call->c_sequence_number], true);
            }

            if ($call->c_created_user_id) {
                //Notifications::socket($call->c_created_user_id, $lead_id = null, 'incomingCall', ['status' => $call->c_call_status, 'duration' => $call->c_call_duration, 'snr' => $call->c_sequence_number], true);
                Notifications::socket($call->c_created_user_id, null, 'webCallUpdate', ['status' => $call->c_call_status, 'duration' => $call->c_call_duration, 'debug' => 'DEFAULT'], true);
            }

        } else {
            $trace[] = 'No call find by params';
        }

        $response['trace'] = $trace;

        return $response;
    }

    /**
     * @param array $post
     * @return array
     */
    private static function prepareDataVoiceFinish(array $post): array
    {
        $data = [];
        if (isset($post['callData'])) {
            $data['CallDataForm'] = $post['callData'];
            unset($post['callData']);
        }
        if (isset($post['call'])) {
            $data['CallForm'] = $post['call'];
            unset($post['call']);
        }
        $data['FinishForm'] = $post;
        return $data;
    }

    /**
     * @param array $post
     */
    public function voiceFinish(array $post): void
    {

        Yii::info(VarDumper::dumpAsString($post), 'info\API:Communication:voiceFinish');

        $originalPost = $post;
        $post = self::prepareDataVoiceFinish($post);

        $finishForm = new FinishForm();
        if (!$finishForm->load($post)) {
            Yii::error('Can not load post data: POST: ' . VarDumper::dumpAsString($originalPost), 'API:CommunicationService:voiceFinish:finishForm:load');
            return;
        }

        if (!$finishForm->validate()) {
            Yii::error(VarDumper::dumpAsString($finishForm->errors), 'API:CommunicationService:voiceFinish:finishForm:validate');
            return;
        }

        if ($finishForm->callData->isEmptySid()) {
            Yii::error('Communication Request: Not found post[callData][sid] ' . VarDumper::dumpAsString($originalPost), 'API:CommunicationService:voiceFinish:post');
            return;
        }

        $call = null;

        if ($finishForm->call->isIncoming()) {
            $call = $this->callRepository->getLastCallByUserCreated($finishForm->callData->sid);
        }

        if (!$call) {
            $call = $this->callRepository->getFirstCall($finishForm->callData->sid);
        }

        if (!$call) {

            $call = Call::create(
                $finishForm->call->c_call_sid,
                $finishForm->call->c_call_type_id,
                $finishForm->call->c_from,
                $finishForm->call->c_to,
                $finishForm->call->c_created_dt,
                $finishForm->call->c_recording_url,
                $finishForm->call->c_recording_duration,
                $finishForm->call->c_caller_name,
                $finishForm->call->c_project_id
            );

            $upp = $this->userProjectParamsRepository->find($call->c_project_id, $call->c_from, $call->c_to);

            if ($upp && $upp->uppUser) {
                $call->setCreatedUser($upp->uppUser->id);
                $call->setProject($upp->upp_project_id);
                Notifications::create($upp->uppUser->id, 'Call ID-' . $call->c_id . ' completed', 'Call ID-' . $call->c_id . ' completed. From ' . $call->c_from . ' to ' . $call->c_to, Notifications::TYPE_INFO, true);
                Notifications::socket($upp->uppUser->id, null, 'getNewNotification', [], true);
            }

        }

        if ($call) {

            if ($finishForm->callData->price) {
                $call->setPrice(abs((float)$finishForm->callData->price));
            }
            if ($finishForm->callData->status && $call->isEmptyStatus()) {
                $call->setStatus($finishForm->callData->status);
            }

            if ($finishForm->callData->status && !$call->isEmptyStatus()) {
                if (!in_array($call->c_call_status, [Call::CALL_STATUS_CANCELED, Call::CALL_STATUS_BUSY, Call::CALL_STATUS_NO_ANSWER], true)) {
                    $call->setStatus($finishForm->callData->status);
                }
            }

            if ($finishForm->callData->duration) {
                $call->setDuration((int)$finishForm->callData->duration);
            }

            if ($lead = $call->cLead) {

                if ($lead->isPending() && $lead->isCallProcessing()) {

                    $delayTimeMin = $lead->getDelayPendingTime();
                    $lead->l_pending_delay_dt = date('Y-m-d H:i:s', strtotime('+' . $delayTimeMin . ' minutes'));
                    $lead->employee_id = null;
                    $lead->callReady();

                    try {
                        $this->leadRepository->save($lead);
                    } catch (\Exception $e) {
                        Yii::error($e, 'API:CommunicationService:voiceFinish:Lead:save');
                    }

                    /*if($call->c_created_user_id) {
                        Notifications::create($call->c_created_user_id, 'Lead delayed -' . $lead->id . '', 'Lead ID-' . $lead->id . ' is delayed. (+'.$delayTimeMin.' minutes)' , Notifications::TYPE_INFO, true);
                        Notifications::socket($call->c_created_user_id, null, 'getNewNotification', [], true);
                    }*/

                }

                if ($lead->isProcessing()) {
                    $lead->callDone();
                    try {
                        $this->leadRepository->save($lead);
                    } catch (\Exception $e) {
                        Yii::error($e, 'API:CommunicationService:voiceFinish:Lead:save:2');
                    }
                }
            }
            try {
                $this->callRepository->save($call);
            } catch (\Exception $e) {
                Yii::error($e, 'API:CommunicationService:voiceFinish:Call:save');
            }

            if ($call->c_created_user_id || $call->c_lead_id) {
                Notifications::socket($call->c_created_user_id, $call->c_lead_id, 'webCallUpdate', ['status' => $call->c_call_status, 'duration' => $call->c_call_duration, 'debug' => 'TYPE_VOIP_FINISH'], true);
            }

        } else {
            Yii::error('Communication Request: Not found Call SID: ' . $finishForm->callData->sid, 'API:Communication:voiceFinish:Call:find');
        }

    }

    /**
     * @param array $post
     * @return array
     */
    private static function prepareDataVoiceClient(array $post): array
    {
        $data = [];
        if (isset($post['callData'])) {
            $data['CallDataForm'] = $post['callData'];
            unset($post['callData']);
        }
        if (isset($post['call'])) {
            $data['CallForm'] = $post['call'];
            unset($post['call']);
        }
        $data['ClientForm'] = $post;
        return $data;
    }

    /**
     * @param array $post
     */
    public function voiceClient(array $post): void
    {

        Yii::info(VarDumper::dumpAsString($post), 'info\API:CommunicationService:voiceClient');

        $originalPost = $post;
        $post = self::prepareDataVoiceClient($post);

        $clientForm = new ClientForm();
        if (!$clientForm->load($post)) {
            Yii::error('Can not load post data: POST: ' . VarDumper::dumpAsString($originalPost), 'API:CommunicationService:voiceClient:clientForm:load');
            return;
        }

        if (!$clientForm->validate()) {
            Yii::error(VarDumper::dumpAsString($clientForm->errors), 'API:CommunicationService:voiceClient:clientForm:validate');
            return;
        }

        if ($clientForm->callData->isEmptySid()) {
            Yii::error('Communication Request: Not found post[callData][sid] / post[callData][CallSid] ' . VarDumper::dumpAsString($originalPost), 'API:CommunicationService:voiceClient:post');
            return;
        }

        $call = null;

        if ($clientForm->call->isIncoming()) {
            $call = $this->callRepository->getLastCallByUserCreated($clientForm->callData->sid);
        }
        if (!$call) {
            $call = $this->callRepository->getFirstCall($clientForm->callData->sid);
        }

        if (!$call) {

            $call = Call::create(
                $clientForm->call->c_call_sid,
                $clientForm->call->c_call_type_id,
                $clientForm->call->c_from,
                $clientForm->call->c_to,
                $clientForm->call->c_created_dt,
                $clientForm->call->c_recording_url,
                $clientForm->call->c_recording_duration,
                $clientForm->call->c_caller_name,
                $clientForm->call->c_project_id
            );

            $upp = $this->userProjectParamsRepository->find($call->c_project_id, $call->c_from, $call->c_to);

            if ($upp && $upp->uppUser) {
                $call->setCreatedUser($upp->uppUser->id);
                $call->setProject($upp->upp_project_id);
                //Notifications::create($upp->uppUser->id, 'Call ID-'.$call->c_id.' completed', 'Call ID-'.$call->c_id.' completed. From ' . $call->c_from .' to '.$call->c_to, Notifications::TYPE_INFO, true);
                //Notifications::socket($upp->uppUser->id, null, 'getNewNotification', [], true);
            }

        }

        if ($call) {

            if ($clientForm->callData->price) {
                $call->setPrice(abs((float)$clientForm->callData->price));
            }

            if ($clientForm->callData->status && $call->isEmptyStatus()) {
                $call->setStatus($clientForm->callData->status);
            }

            if ($clientForm->callData->status && !$call->isEmptyStatus()) {
                if (!in_array($call->c_call_status, [Call::CALL_STATUS_CANCELED, Call::CALL_STATUS_BUSY, Call::CALL_STATUS_NO_ANSWER], true)) {
                    $call->setStatus($clientForm->callData->status);
                }
            }

            if ($clientForm->callData->duration) {
                $call->setDuration((int)$clientForm->callData->duration);
            }

            if ($lead = $call->cLead) {

                if ($lead->isPending() && $lead->isCallProcessing()) {

                    $delayTimeMin = $lead->getDelayPendingTime();
                    $lead->l_pending_delay_dt = date('Y-m-d H:i:s', strtotime('+' . $delayTimeMin . ' minutes'));
                    $lead->employee_id = null;
                    $lead->callReady();

                    try {
                        $this->leadRepository->save($lead);
                    } catch (\Exception $e) {
                        Yii::error($e, 'API:CommunicationService:voiceClient:Lead:save');
                    }

                    /*if($call->c_created_user_id) {
                        Notifications::create($call->c_created_user_id, 'Lead delayed -' . $lead->id . '', 'Lead ID-' . $lead->id . ' is delayed. (+'.$delayTimeMin.' minutes)' , Notifications::TYPE_INFO, true);
                        Notifications::socket($call->c_created_user_id, null, 'getNewNotification', [], true);
                    }*/

                }

                if ($lead->isProcessing()) {
                    $lead->callDone();
                    try {
                        $this->leadRepository->save($lead);
                    } catch (\Exception $e) {
                        Yii::error($e, 'API:CommunicationService:voiceClient:Lead:save:2');
                    }
                }
            }

            try {
                $this->callRepository->save($call);
            } catch (\Exception $e) {
                Yii::error($e, 'API:CommunicationService:voiceClient:Call:save');
            }

            if ($call->c_created_user_id || $call->c_lead_id) {
                Notifications::socket($call->c_created_user_id, $call->c_lead_id, 'webCallUpdate', ['status' => $call->c_call_status, 'duration' => $call->c_call_duration, 'debug' => 'TYPE_VOIP_CLIENT'], true);
            }

        } else {
            Yii::error('Communication Request: Not found Call SID: ' . $clientForm->callData->sid, 'API:Communication:voiceClient:Call:find');
        }

    }

    /**
     * @param array $post
     * @return array
     */
    private static function prepareDataVoiceRecord(array $post): array
    {
        $data = [];
        if (isset($post['callData'])) {
            $data['CallDataForm'] = $post['callData'];
            unset($post['callData']);
        }
        if (isset($post['call'])) {
            $data['CallForm'] = $post['call'];
            unset($post['call']);
        }
        $data['RecordForm'] = $post;
        return $data;
    }

    /**
     * @param array $post
     */
    public function voiceRecord(array $post): void
    {
        Yii::info(VarDumper::dumpAsString($post), 'info\API:CommunicationService:voiceRecord');

        $originalPost = $post;
        $post = self::prepareDataVoiceRecord($post);

        $recordForm = new RecordForm();
        $recordForm->load($post);

        if (!$recordForm->load($post)) {
            Yii::error('Can not load post data: POST: ' . VarDumper::dumpAsString($originalPost), 'API:CommunicationService:voiceRecord:recordForm:load');
            return;
        }

        if (!$recordForm->validate()) {
            Yii::error(VarDumper::dumpAsString($recordForm->errors), 'API:CommunicationService:voiceRecord:recordForm:validate');
            return;
        }

        if ($recordForm->callData->isEmptyCallSid()) {
            Yii::error('CallSid is empty', 'API:CommunicationService:voiceRecord:recordForm:empty:CallSid');
            return;
        }

        if ($recordForm->callData->isEmptyRecordingUrl()) {
            Yii::error('RecordingUrl is empty', 'API:CommunicationService:voiceRecord:recordForm:empty:RecordingUrl');
            return;
        }

        $call = null;

        if ($recordForm->call->isIncoming()) {
            $call = $this->callRepository->getLastCallByUserCreated($recordForm->callData->CallSid);
        }
        if (!$call) {
            $call = $this->callRepository->getFirstCall($recordForm->callData->CallSid);
        }

        if (!$call) {
            return;
        }

        $call->updateRecordingData(
            $recordForm->callData->RecordingUrl,
            $recordForm->callData->RecordingSid,
            $recordForm->callData->RecordingDuration
        );

        try {
            $this->callRepository->save($call);
        } catch (\Exception $e) {
            Yii::error($e, 'API:CommunicationService:voiceRecord:Call:save');
        }

        if ($call->c_lead_id) {
            if ($call->c_created_user_id) {
                Notifications::create($call->c_created_user_id, 'Call Recording Completed  from ' . $call->c_from . ' to ' . $call->c_to . ' <br>Lead ID: ' . $call->c_lead_id, Notifications::TYPE_INFO, true);
            }
            Notifications::socket(null, $call->c_lead_id, 'recordingUpdate', ['url' => $call->c_recording_url], true);
        }

    }

    /**
     * @param array $post
     * @return array
     */
    private static function prepareDataVoiceIncoming(array $post): array
    {
        $data = [];
        if (isset($post['call'])) {
            $data['CallForm'] = $post['call'];
            unset($post['call']);
        }
        $data['IncomingForm'] = $post;
        return $data;
    }

    public function voiceIncoming(string $type, array $post): array
    {

        Yii::info(VarDumper::dumpAsString($post), 'info\API:Communication:voiceIncoming');

        $originalPost = $post;
        $post = self::prepareDataVoiceIncoming($post);

        $incomingForm = new IncomingForm();
        $incomingForm->load($post);

        if (!$incomingForm->load($post)) {
            Yii::error('Can not load post data: POST: ' . VarDumper::dumpAsString($originalPost), 'API:CommunicationService:voiceIncoming:incomingForm:load');
            return [];
        }

        if (!$incomingForm->validate()) {
            Yii::error(VarDumper::dumpAsString($incomingForm->errors), 'API:CommunicationService:voiceIncoming:incomingForm:validate');
            return [];
        }

        $response = [];

        if ($incomingForm->call->isEmpty()) {
            $response['error'] = 'Not found "call" array';
            $response['error_code'] = 12;
            Yii::error('Can not load "call" array: POST: ' . VarDumper::dumpAsString($originalPost), 'API:CommunicationService:voiceIncoming:incomingForm:load:call');
            return $response;
        }

        $settings = new CommunicationSettings();

        $callSession = null;
        if ($incomingForm->call->CallSid && $settings->use_voice_gather) {
            $callSession = $this->callSessionRepository->getBySid($incomingForm->call->CallSid);
        }

        if (!$incomingForm->call->callerPhone) {
            $response['error'] = 'Not found Call From (Client phone number)';
            $response['error_code'] = 10;
        }

        if (!$incomingForm->call->calledPhone) {
            $response['error'] = 'Not found Call Called (Agent phone number)';
            $response['error_code'] = 11;
        }

        $clientPhone = $this->clientPhoneRepository->getByPhone($incomingForm->call->callerPhone);

        $source = $this->sourceRepository->getByPhone($incomingForm->call->calledPhone);

        $callSourceTypeId = null;
        $lead2 = null;


        $isError = false;

        $isOnHold = false;
        $callGeneralNumber = false;
        $call_project_id = null;
        $call_agent_username = [];
        $call_employee = [];
        $agentDirectCallCheck = false;

        if (!$source) {
            $agentDirectCallCheck = true;
        }

        $get_data = Yii::$app->request->get();

        // detect by sources
        if ($source && $project = $source->project) {

            $callSourceTypeId = Call::SOURCE_GENERAL_LINE;

            if ($type === self::TYPE_VOIP_INCOMING) {

                if ($clientPhone) {
                    $lead2 = Lead2::findLastLeadByClientPhone($incomingForm->call->callerPhone, $project->id);
                }

                if (!$lead2) {
                    // $sql = Lead2::findLastLeadByClientPhone($incomingForm->call->callerPhone, $project->id, true);
                    // Yii::info('phone: '. $incomingForm->call->callerPhone.', sql: '. $sql, 'info\API:Communication:findLastLeadByClientPhone');
                    $lead2 = Lead2::createNewLeadByPhone($incomingForm->call->callerPhone, $project->id);
                } else {
                    Yii::info('Find LastLead (' . $lead2->id . ') By ClientPhone: ' . $incomingForm->call->callerPhone, 'info\API:Communication:voiceIncoming:findLastLeadByClientPhone');
                }
            }


            if ($settings->use_voice_gather) {
                // check if is first call or is redirect from Gather
                if ($callSession && isset($get_data['step']) && (int)$get_data['step'] === 1) {
                    return $this->voiceGatherSteps($incomingForm->call->CallSid, $source, $project, $incomingForm->call->callerPhone, 1);
                }

                if ($callSession && isset($get_data['step']) && (int)$get_data['step'] === 2) {
                    return $this->actionVoiceGather();
                }

                if (!$callSession && !$incomingForm->call->ParentCallSid) {
                    return $this->voiceGatherSteps($$incomingForm->call->callSid, $source, $project, $incomingForm->call->callerPhone, 1);
                }

                if ($incomingForm->call->Digits && $callSession && !$incomingForm->call->ParentCallSid) {
                    return $this->actionVoiceGather();
                }
            }

            $call_project_id = $project->id;
            $project_employee_access = ProjectEmployeeAccess::find()->where(['project_id' => $project->id])->all();
            $callAgents = [];
            $agents_ids = [];
            if ($settings->use_general_line_distribution) {
                $clientIds = [];

                $log_data = [
                    'find_online_agents' => 'no data',
                    'project_id' => $call_project_id,
                    'called_phone' => $incomingForm->call->calledPhone,
                    'client_phone' => $incomingForm->call->callerPhone,
                    'client_ids' => $clientIds ? implode(',', $clientIds) : '',
                    'agents_ids' => $agents_ids ? implode(',', $agents_ids) : '',
                ];

                try {
                    // FIRST STEP TO DETECT AGENTS FOR CALL.  SL-370
                    if ($clientPhone && $clientPhone->client && $clientPhone->client->id) {
                        /*$clientIdsQuery = ClientPhone::findBySql("SELECT GROUP_CONCAT(client_id) AS client_ids FROM " . ClientPhone::tableName() . "  WHERE phone = '{$incomingForm->call->callerPhone}' ")
                            ->asArray()->one();
                        if (isset($clientIdsQuery['client_ids']) && $clientIdsQuery['client_ids']) {
                            $clientIds = explode(',', $clientIdsQuery['client_ids']);
                        }*/
                        $clientIds = ClientPhone::find()->select(['client_id'])->where(['phone' => $incomingForm->call->callerPhone])->column();

                        $latest_client_leads = Lead::find()
                            ->select(['DISTINCT(employee_id)', 'updated'])
                            ->where(['IN', 'client_id', $clientIds])
                            ->andWhere(['project_id' => $call_project_id])
                            ->andWhere(['<>', 'status', Lead::STATUS_TRASH])
                            ->orderBy(['updated' => SORT_DESC])
                            ->limit($settings->general_line_leads_limit)->all();

                        if ($latest_client_leads) {
                            foreach ($latest_client_leads AS $client_lead) {
                                if ($client_lead->employee && $client_lead->employee->userProfile->up_call_type_id === UserProfile::CALL_TYPE_WEB) {
                                    if ($client_lead->employee->isOnline() && $client_lead->employee->isCallStatusReady() && $client_lead->employee->isCallFree()) {
                                        $callAgents[] = $client_lead->employee;
                                        $agents_ids[] = $client_lead->employee->id . ' (' . $client_lead->employee->username . ')' . print_r($client_lead->employee->getRolesRaw(), true);
                                    }
                                }
                            }
                        }
                    }

                    // SECOND STEP TO DETECT AGENTS FOR CALL.  SL-370
                    if (!$callAgents && $project_employee_access) {
                        $only_agents = [];
                        $only_supervisors = [];

                        $agents_for_call = Employee::getAgentsForGeneralLineCall($call_project_id, $incomingForm->call->calledPhone, $settings->general_line_last_hours);
                        if ($agents_for_call) {
                            foreach ($agents_for_call AS $agentForCall) {
                                $agentId = (int)$agentForCall['tbl_user_id'];
                                $agentObject = Employee::findOne($agentId);
                                if (!$agentObject) {
                                    continue;
                                }
                                if ($agentObject->userProfile && $agentObject->userProfile->up_call_type_id !== UserProfile::CALL_TYPE_WEB) {
                                    continue;
                                }
                                $agents_ids[] = $agentObject->id . ' : ' . $agentObject->username . ' - ' . print_r($agentObject->getRolesRaw(), true);
                                $roles = $agentObject->getRolesRaw();
                                if (array_key_exists('agent', $roles)) {
                                    $only_agents[] = $agentObject;
                                }
                                if (array_key_exists('supervision', $roles)) {
                                    $only_supervisors[] = $agentObject;
                                }
                                if ((int)$settings->general_line_role_priority > 0) {
                                    $callAgents = $only_agents;
                                    if (!count($callAgents) && count($only_supervisors)) {
                                        $callAgents = $only_supervisors;
                                    }
                                } else {
                                    $callAgents = array_merge($only_agents, $only_supervisors);
                                }
                            }
                        }

                        $log_data = [
                            'find_online_agents' => 'no',
                            'project_id' => $call_project_id,
                            'called_phone' => $incomingForm->call->calledPhone,
                            'client_phone' => $incomingForm->call->callerPhone,
                            'client_ids' => $clientIds ? implode(',', $clientIds) : '',
                            'agents_ids' => $agents_ids ? implode(',', $agents_ids) : '',
                        ];

                    } else {
                        $log_data = [
                            'find_online_agents' => 'yes',
                            'project_id' => $call_project_id,
                            'called_phone' => $incomingForm->call->calledPhone,
                            'client_phone' => $incomingForm->call->callerPhone,
                            'client_ids' => $clientIds ? implode(',', $clientIds) : '',
                            'agents_ids' => $agents_ids ? implode(',', $agents_ids) : '',
                        ];
                    }
                    \Yii::info(VarDumper::dumpAsString($log_data, 10, false), 'info\API:Communication:voiceIncoming:new_general_line_distribution');
                } catch (\Throwable $ee) {
                    \Yii::error(VarDumper::dumpAsString(['log_data' => $log_data, 'errors' => $ee]), 'API:Communication:voiceIncoming:general_line_distribution');
                    $callAgents = [];
                }
            }

            if ($project_employee_access && !$callAgents) {
                foreach ($project_employee_access AS $projectEmployer) {
                    $projectUser = $projectEmployer->employee; //Employee::findOne($projectEmployer->employee_id);
                    if ($projectUser && $projectUser->userProfile && $projectUser->userProfile->up_call_type_id === UserProfile::CALL_TYPE_WEB) {
                        $callAgents[] = $projectUser;
                    }
                }
            }

            $agentsInfo = [];
            if ($callAgents) {
                $cntCallAgents = 1;
                foreach ($callAgents AS $user) {
                    if ($user->isOnline()) {
                        if ($user->isCallStatusReady()) {
                            if ($user->isCallFree()) {
                                if (in_array('seller' . $user->id, $call_agent_username)) {
                                    continue;
                                }
                                if ($cntCallAgents > $settings->general_line_user_limit) {
                                    break;
                                }
                                //Yii::info('DIRECT - User (' . $user->username . ') Id: ' . $user->id . ', phone: ' . $incomingForm->call->calledPhone, 'info\API:CommunicationController:actionVoice:Direct - 2');
                                $agentsInfo[] = 'DIRECT - User (' . $user->username . ') Id: ' . $user->id . ', phone: ' . $incomingForm->call->calledPhone;
                                $isOnHold = false;
                                $call_agent_username[] = 'seller' . $user->id;
                                $call_employee[] = $user;
                                //break;
                                $cntCallAgents++;
                            } else {
                                $agentsInfo[] = 'Call Occupied - User (' . $user->username . ') Id: ' . $user->id . ', phone: ' . $incomingForm->call->calledPhone;

                                //Yii::info('Call Occupied - User ('.$user->username.') Id: '.$user->id.', phone: ' . $incomingForm->call->calledPhone, 'info\API:CommunicationController:actionVoice:isCallFree');
                                //Notifications::create($user->id, 'Missing Call [Occupied]', 'Missing Call from ' . $incomingForm->call->callerPhone .' to '.$incomingForm->call->calledPhone . "\r\n Reason: Agent Occupied", Notifications::TYPE_WARNING, true);
                                //Notifications::socket($user->id, null, 'getNewNotification', [], true);
                            }
                        } else {
                            // Yii::info('Call Status not Ready - User ('.$user->username.') Id: '.$user->id.', phone: ' . $incomingForm->call->calledPhone, 'info\API:CommunicationController:actionVoice:isCallStatusReady');
                            // Notifications::create($user->id, 'Missing Call [not Ready]', 'Missing Call from ' . $incomingForm->call->callerPhone .' to '.$incomingForm->call->calledPhone . "\r\n Reason: Call Status not Ready", Notifications::TYPE_WARNING, true);
                            //Notifications::socket($user->id, null, 'getNewNotification', [], true);
                        }
                    } else {
                        //Yii::info('Offline - User ('.$user->username.') Id: '.$user->id.', phone: ' . $incomingForm->call->calledPhone, 'info\API:CommunicationController:actionVoice:isOnline');
                        //Notifications::create($user->id, 'Missing Call [Offline]', 'Missing Call from ' . $incomingForm->call->callerPhone .' to '.$incomingForm->call->calledPhone . "\r\n Reason: Agent offline", Notifications::TYPE_WARNING, true);
                        //Notifications::socket($user->id, null, 'getNewNotification', [], true);
                    }
                }
                if (!$call_employee) {
                    $isOnHold = true;
                }
            } else {
                $isOnHold = true;
                Yii::info('Call in Hold. phone: ' . $incomingForm->call->calledPhone, 'info\API:Communication:voiceIncoming:CallInHold');
            }

            if ($agentsInfo) {
                Yii::info(VarDumper::dumpAsString($agentsInfo), 'info\API:Communication:voiceIncoming:isCallFree');
            }

        } elseif ($agentDirectCallCheck) {

            $agentRes = $this->getDirectAgentsByPhoneNumber($incomingForm->call->calledPhone, $incomingForm->call->callerPhone, $settings->direct_agent_user_limit);
            if ($agentRes && isset($agentRes['call_employee'], $agentRes['call_agent_username']) && $agentRes['call_employee']) {
                $isOnHold = false;
                $callGeneralNumber = false;
                $call_employee = $agentRes['call_employee'];
                $call_project_id = $agentRes['call_project_id'] ?? null;
                $call_agent_username = $agentRes['call_agent_username'];
            } else {
                if ($agentRes && isset($agentRes['call_project_id'])) {
                    $call_project_id = ($agentRes['call_project_id'] > 0) ? $agentRes['call_project_id'] : null;
                    if (NULL === $call_project_id) {
                        $isOnHold = false;
                        $callGeneralNumber = true;
                    } else {
                        $isOnHold = true;
                        $callGeneralNumber = false;
                    }
                } else {
                    $isOnHold = false;
                    $callGeneralNumber = true;
                }
            }


            if ($clientPhone) {
                $lead2 = Lead2::findLastLeadByClientPhone($incomingForm->call->callerPhone, $agentRes['call_project_id'] ?? null);
            }

            if (!$lead2) {
                //$sql = Lead2::findLastLeadByClientPhone($incomingForm->call->callerPhone, true);
                //Yii::info('phone: '. $incomingForm->call->callerPhone.', sql: '. $sql, 'info\API:Communication:findLastLeadByClientPhone');
                if (isset($agentRes['call_project_id']) && $agentRes['call_project_id']) {
                    $lead2 = Lead2::createNewLeadByPhone($incomingForm->call->callerPhone, $agentRes['call_project_id']);
                }
            } /*else {
                            Yii::info('Find LastLead ('.$lead2->id.') By ClientPhone: ' . $incomingForm->call->callerPhone, 'info\API:Communication:findLastLeadByClientPhone');
                        }*/


        } else {
            $callGeneralNumber = true;
        }

        // $clientPhone = ClientPhone::find()->where(['phone' => $incomingForm->call->callerPhone])->orderBy(['id' => SORT_DESC])->limit(1)->one();
        //$lead = null;

        //if(!$lead) {
        /*if ($clientPhone && $clientPhone->client_id) {
            $lead = Lead::find()->select(['id'])->where(['client_id' => $clientPhone->client_id])->orderBy(['id' => SORT_DESC])->limit(1)->one();
        }*/
        //}

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

        if ($clientPhone && $client = $clientPhone->client) {
            $data['client_name'] = $client->full_name;
            $data['client_id'] = $clientPhone->client_id;
            $data['client_created_date'] = Yii::$app->formatter->asDate(strtotime($client->created));
            if ($lead2) {
                $data['last_lead_id'] = $lead2->id;
                $data['client_last_activity'] = Yii::$app->formatter->asDate(strtotime($client->created));
            }
        }

        $data['client_phone'] = $incomingForm->call->callerPhone;
        $data['agent_phone'] = $incomingForm->call->calledPhone;


        Yii::info(VarDumper::dumpAsString([
            'data' => $data,
            'post' => $post,
            'call_employee' => $call_employee,

        ], 10, false), 'info\API:Communication:voiceIncoming:ParamsToCall');

        if (!$isOnHold && !$callGeneralNumber && $call_employee) {

            foreach ($call_employee AS $key => $userCall) {
                $call = new Call();
                $call->c_call_sid = $post['call']['CallSid'] ?? null;
                $call->c_call_type_id = Call::CALL_TYPE_IN;
                $call->c_call_status = $post['call']['CallStatus'] ?? Call::CALL_STATUS_RINGING;
                $call->c_com_call_id = $post['call_id'] ?? null;
                $call->c_project_id = $call_project_id;
                $call->c_is_new = true;
                $call->c_created_dt = date('Y-m-d H:i:s');
                $call->c_from = $incomingForm->call->callerPhone;
                $call->c_to = $incomingForm->call->calledPhone; //$userCall->username ? $userCall->username : null;
                $call->c_created_user_id = $userCall->id;
                $call->c_source_type_id = Call::SOURCE_REDIRECT_CALL;
                if ($lead2) {
                    $call->c_lead_id = $lead2->id;
                } else {
                    $call->c_lead_id = null;
                }
                if (!$call->save()) {
                    Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceIncoming:Call:save');
                }
                $data['status'] = $call->c_call_status;
                // Notifications::socket($call->c_created_user_id, $call->c_lead_id, 'incomingCall', $data, true);
            }
        } elseif ($isOnHold) {
            $call = new Call();
            $call->c_call_sid = $post['call']['CallSid'] ?? null;
            $call->c_call_type_id = Call::CALL_TYPE_IN;
            $call->c_call_status = Call::CALL_STATUS_QUEUE;
            $call->c_com_call_id = $post['call_id'] ?? null;
            $call->c_project_id = $call_project_id;
            $call->c_is_new = true;
            $call->c_created_dt = date('Y-m-d H:i:s');
            $call->c_from = $incomingForm->call->callerPhone;
            $call->c_to = $incomingForm->call->calledPhone;
            $call->c_created_user_id = null;
            $call->c_source_type_id = $callSourceTypeId;
            if ($lead2) {
                $call->c_lead_id = $lead2->id;
            }
            if (!$call->save()) {
                Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceIncoming:save:isOnHold');
            }

            if ($call_project_id) {
                $project = Project::findOne($call_project_id);
            } else {
                $project = null;
            }

            $responseTwml = new VoiceResponse();

            $url_say_play_hold = '';
            $url_music_play_hold = 'https://talkdeskapp.s3.amazonaws.com/production/audio_messages/folk_hold_music.mp3';

            if ($project && $project->custom_data) {
                $customData = @json_decode($project->custom_data, true);
                if ($customData) {
                    if (isset($customData['url_say_play_hold']) && $customData['url_say_play_hold']) {
                        $url_say_play_hold = $customData['url_say_play_hold'];
                    }

                    if (isset($customData['url_music_play_hold']) && $customData['url_music_play_hold']) {
                        $url_music_play_hold = $customData['url_music_play_hold'];
                    }
                }
            }

            if ($url_say_play_hold) {
                $responseTwml->play($url_say_play_hold);
                if ($url_music_play_hold) {
                    $responseTwml->play($url_music_play_hold);
                }

            } else {


                $say_params = \Yii::$app->params['voice_gather'];
                $responseTwml = new VoiceResponse();
                $responseTwml->pause(['length' => 5]);

                $company = ' ' . strtolower($project->name);
                $entry_phrase = str_replace('{{project}}', $company, $say_params['entry_phrase']);
                $responseTwml->say('    ' . $entry_phrase . '  ' . $say_params['languages'][1]['hold_voice'], [
                    'language' => $say_params['languages'][1]['language'],
                    'voice' => $say_params['languages'][1]['voice'],
                ]);
                $responseTwml->play($say_params['hold_play']);
                $response['twml'] = (string)$responseTwml;
            }

            $response['twml'] = (string)$responseTwml;

            Yii::info('Call add to hold : call_project_id: ' . $call_project_id . ', generalLine: ' . $settings->generalLineNumber . ', TWML: ' . $response['twml'], 'info\API:Communication:voiceIncoming:isOnHold - 5');

        } elseif ($callGeneralNumber) {
            $call = new Call();
            $call->c_call_sid = $post['call']['CallSid'] ?? null;
            $call->c_call_type_id = Call::CALL_TYPE_IN;
            $call->c_call_status = $post['call']['CallStatus'] ?? Call::CALL_STATUS_RINGING;
            $call->c_com_call_id = $post['call_id'] ?? null;
            $call->c_project_id = $call_project_id;
            $call->c_is_new = true;
            $call->c_created_dt = date('Y-m-d H:i:s');
            $call->c_from = $incomingForm->call->callerPhone;
            $call->c_to = $settings->generalLineNumber;
            $call->c_created_user_id = null;
            $call->c_source_type_id = $callSourceTypeId;
            if ($lead2) {
                $call->c_lead_id = $lead2->id;
            }
            if (!$call->save()) {
                Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceIncoming:Call:save:callGeneralNumber');
            }
            Yii::info('Redirected to General Line : call_project_id: ' . $call_project_id . ', generalLine: ' . $settings->generalLineNumber, 'info\API:Communication:voiceIncoming:callGeneralNumber - 6');
        } else {
            if (!$isOnHold && !$callGeneralNumber) {
                $isError = true;
                Yii::error('Not found call destination agent, hold or general line for call number:' . $incomingForm->call->calledPhone, 'API:Communication:voiceIncoming:isOnHold_callGeneralNumber');
            }
        }

        if (!$isError) {
            $response['agent_sip'] = '';
            $response['agent_phone_number'] = $incomingForm->call->calledPhone;
            $response['client_phone_number'] = $incomingForm->call->callerPhone;
            $response['general_phone_number'] = $settings->generalLineNumber;
            $response['agent_username'] = $call_agent_username;
            $response['call_to_hold'] = $isOnHold ? 1 : 0;
            $response['call_to_general'] = $callGeneralNumber ? 1 : 0;
        } else {
            $response['error'] = 'Not found call destination agent, hold or general line';
            $response['error_code'] = 13;
        }


        return $response;
    }
//
//    public function voiceIncomingOld(string $type, array $post): array
//    {
//        $response = [];
//
//        Yii::info(VarDumper::dumpAsString($post), 'info\API:Communication:voiceIncoming');
//
//        $incomingCallForm = new IncomingCallForm();
//
//        if (!$incomingCallForm->load($post, 'call')) {
//            $response['error'] = 'Not found "call" array';
//            $response['error_code'] = 12;
//            return $response;
//        }
//
//        $callSourceTypeId = null;
//        $lead2 = null;
//        $generalLineSettings = new GeneralLineSettings();
//        $callSession = null;
//        $isError = false;
//        $clientPhone = null;
//        $generalLineNumber = Yii::$app->params['global_phone'];
//        $use_voice_gather = Yii::$app->params['voice_gather']['use_voice_gather'] ?? false;
//
//        if ($incomingCallForm->CallSid && $use_voice_gather) {
//            $callSession = CallSession::findOne(['cs_cid' => $incomingCallForm->CallSid]);
//        }
//
//        if (!$incomingCallForm->callerPhone) {
//            $response['error'] = 'Not found Call From (Client phone number)';
//            $response['error_code'] = 10;
//        }
//
//        if (!$incomingCallForm->calledPhone) {
//            $response['error'] = 'Not found Call Called (Agent phone number)';
//            $response['error_code'] = 11;
//        }
//
//        $isOnHold = false;
//        $callGeneralNumber = false;
//        $call_project_id = null;
//        $call_agent_username = [];
//        $call_employee = [];
//
//        $clientPhone = ClientPhone::find()->where(['phone' => $incomingCallForm->callerPhone])->orderBy(['id' => SORT_DESC])->limit(1)->one();
//
//        $source = Sources::findOne(['phone_number' => $incomingCallForm->calledPhone]);
//
//        // detect by sources
//        if ($source && $project = $source->project) {
//
//            $callSourceTypeId = Call::SOURCE_GENERAL_LINE;
//
//            if ($type === self::TYPE_VOIP_INCOMING) {
//
//                if ($clientPhone) {
//                    $lead2 = Lead2::findLastLeadByClientPhone($clientPhone->phone, $project->id);
//                }
//
//                if (!$lead2) {
//                    // $sql = Lead2::findLastLeadByClientPhone($client_phone_number, $project->id, true);
//                    // Yii::info('phone: '. $client_phone_number.', sql: '. $sql, 'info\API:Communication:findLastLeadByClientPhone');
//                    $lead2 = Lead2::createNewLeadByPhone($incomingCallForm->callerPhone, $project->id);
//                } else {
//                    Yii::info('Find LastLead (' . $lead2->id . ') By ClientPhone: ' . $incomingCallForm->callerPhone, 'info\API:Communication:voiceIncoming:findLastLeadByClientPhone');
//                }
//            }
//
//            if ($use_voice_gather) {
//
//                $step = (int)Yii::$app->request->get('step');
//
//                // check if is first call or is redirect from Gather
//                if ($callSession) {
//                    if ($step === 1) {
//                        return $this->voiceGatherSteps($incomingCallForm->CallSid, $source, $project, $incomingCallForm->callerPhone, 1);
//                    }
//                    if ($step === 2) {
//                        return $this->actionVoiceGather();
//                    }
//                }
//
//                if (!$callSession && !$incomingCallForm->ParentCallSid) {
//                    return $this->voiceGatherSteps($incomingCallForm->CallSid, $source, $project, $incomingCallForm->callerPhone, 1);
//                }
//
//                if ($incomingCallForm->Digits && $callSession && !$incomingCallForm->ParentCallSid) {
//                    return $this->actionVoiceGather();
//                }
//            }
//
//            $call_project_id = $project->id;
//            $project_employee_access = ProjectEmployeeAccess::find()->where(['project_id' => $project->id])->all();
//            //Yii::info(VarDumper::dumpAsString($project_employee_access), 'info\API:CommunicationController:actionVoice:$project_employee_access');
//            $callAgents = [];
//            $agents_ids = [];
//            if ($generalLineSettings->use_general_line_distribution) {
//                $clientIds = [];
//
//                $log_data = [
//                    'find_online_agents' => 'no data',
//                    'project_id' => $call_project_id,
//                    'called_phone' => $incomingCallForm->calledPhone,
//                    'client_phone' => $incomingCallForm->callerPhone,
//                    'client_ids' => $clientIds ? implode(',', $clientIds) : '',
//                    'agents_ids' => $agents_ids ? implode(',', $agents_ids) : '',
//                ];
//
//                try {
//                    // FIRST STEP TO DETECT AGENTS FOR CALL.  SL-370
//                    if ($clientPhone && $clientPhone->client && $clientPhone->client->id) {
//                        /*$clientIdsQuery = ClientPhone::findBySql("SELECT GROUP_CONCAT(client_id) AS client_ids FROM " . ClientPhone::tableName() . "  WHERE phone = '{$client_phone_number}' ")
//                            ->asArray()->one();
//                        if (isset($clientIdsQuery['client_ids']) && $clientIdsQuery['client_ids']) {
//                            $clientIds = explode(',', $clientIdsQuery['client_ids']);
//                        }*/
//                        $clientIds = ClientPhone::find()->select(['client_id'])->where(['phone' => $incomingCallForm->callerPhone])->column();
//
//                        $latest_client_leads = Lead::find()
//                            ->select(['DISTINCT(employee_id)', 'updated'])
//                            ->where(['IN', 'client_id', $clientIds])
//                            ->andWhere(['project_id' => $call_project_id])
//                            ->andWhere(['<>', 'status', Lead::STATUS_TRASH])
//                            ->orderBy(['updated' => SORT_DESC])
//                            ->limit($generalLineSettings->general_line_leads_limit)->all();
//
//                        if ($latest_client_leads) {
//                            foreach ($latest_client_leads AS $client_lead) {
//                                if ($client_lead->employee && $client_lead->employee->userProfile->up_call_type_id === UserProfile::CALL_TYPE_WEB) {
//                                    if ($client_lead->employee->isOnline() && $client_lead->employee->isCallStatusReady() && $client_lead->employee->isCallFree()) {
//                                        $callAgents[] = $client_lead->employee;
//                                        $agents_ids[] = $client_lead->employee->id . ' (' . $client_lead->employee->username . ')' . print_r($client_lead->employee->getRolesRaw(), true);
//                                    }
//                                }
//                            }
//                        }
//                    }
//
//                    // SECOND STEP TO DETECT AGENTS FOR CALL.  SL-370
//                    if (!$callAgents && $project_employee_access) {
//                        $only_agents = [];
//                        $only_supervisors = [];
//
//                        $agents_for_call = Employee::getAgentsForGeneralLineCall($call_project_id, $incomingCallForm->calledPhone, $generalLineSettings->general_line_last_hours);
//                        if ($agents_for_call) {
//                            foreach ($agents_for_call AS $agentForCall) {
//                                $agentId = (int)$agentForCall['tbl_user_id'];
//                                $agentObject = Employee::findOne($agentId);
//                                if (!$agentObject) {
//                                    continue;
//                                }
//                                if ($agentObject->userProfile && $agentObject->userProfile->up_call_type_id !== UserProfile::CALL_TYPE_WEB) {
//                                    continue;
//                                }
//                                $agents_ids[] = $agentObject->id . ' : ' . $agentObject->username . ' - ' . print_r($agentObject->getRolesRaw(), true);
//                                $roles = $agentObject->getRolesRaw();
//                                if (array_key_exists('agent', $roles)) {
//                                    $only_agents[] = $agentObject;
//                                }
//                                if (array_key_exists('supervision', $roles)) {
//                                    $only_supervisors[] = $agentObject;
//                                }
//                                if ($generalLineSettings->general_line_role_priority > 0) {
//                                    $callAgents = $only_agents;
//                                    if (!count($callAgents) && count($only_supervisors)) {
//                                        $callAgents = $only_supervisors;
//                                    }
//                                } else {
//                                    $callAgents = array_merge($only_agents, $only_supervisors);
//                                }
//                            }
//                        }
//
//                        $log_data = [
//                            'find_online_agents' => 'no',
//                            'project_id' => $call_project_id,
//                            'called_phone' => $incomingCallForm->calledPhone,
//                            'client_phone' => $incomingCallForm->callerPhone,
//                            'client_ids' => $clientIds ? implode(',', $clientIds) : '',
//                            'agents_ids' => $agents_ids ? implode(',', $agents_ids) : '',
//                        ];
//
//                    } else {
//                        $log_data = [
//                            'find_online_agents' => 'yes',
//                            'project_id' => $call_project_id,
//                            'called_phone' => $incomingCallForm->calledPhone,
//                            'client_phone' => $incomingCallForm->callerPhone,
//                            'client_ids' => $clientIds ? implode(',', $clientIds) : '',
//                            'agents_ids' => $agents_ids ? implode(',', $agents_ids) : '',
//                        ];
//                    }
//                    \Yii::info(VarDumper::dumpAsString($log_data, 10, false), 'info\API:Communication:voiceIncoming:new_general_line_distribution');
//                } catch (\Throwable $ee) {
//                    \Yii::error(VarDumper::dumpAsString(['log_data' => $log_data, 'errors' => $ee]), 'API:Communication:voiceIncoming:general_line_distribution');
//                    $callAgents = [];
//                }
//            }
//
//            if ($project_employee_access && !$callAgents) {
//                foreach ($project_employee_access AS $projectEmployer) {
//                    $projectUser = $projectEmployer->employee; //Employee::findOne($projectEmployer->employee_id);
//                    if ($projectUser && $projectUser->userProfile && $projectUser->userProfile->up_call_type_id === UserProfile::CALL_TYPE_WEB) {
//                        $callAgents[] = $projectUser;
//                    }
//                }
//            }
//
//            $agentsInfo = [];
//            if ($callAgents) {
//                $cntCallAgents = 1;
//                foreach ($callAgents AS $user) {
//                    if ($user->isOnline()) {
//                        if ($user->isCallStatusReady()) {
//                            if ($user->isCallFree()) {
//                                if (in_array('seller' . $user->id, $call_agent_username)) {
//                                    continue;
//                                }
//                                if ($cntCallAgents > $generalLineSettings->general_line_user_limit) {
//                                    break;
//                                }
//                                //Yii::info('DIRECT - User (' . $user->username . ') Id: ' . $user->id . ', phone: ' . $incomingForm->call->calledPhone, 'info\API:CommunicationController:actionVoice:Direct - 2');
//                                $agentsInfo[] = 'DIRECT - User (' . $user->username . ') Id: ' . $user->id . ', phone: ' . $incomingCallForm->calledPhone;
//                                $isOnHold = false;
//                                $call_agent_username[] = 'seller' . $user->id;
//                                $call_employee[] = $user;
//                                //break;
//                                $cntCallAgents++;
//                            } else {
//                                $agentsInfo[] = 'Call Occupied - User (' . $user->username . ') Id: ' . $user->id . ', phone: ' . $incomingCallForm->calledPhone;
//
//                                //Yii::info('Call Occupied - User ('.$user->username.') Id: '.$user->id.', phone: ' . $incomingForm->call->calledPhone, 'info\API:CommunicationController:actionVoice:isCallFree');
//                                //Notifications::create($user->id, 'Missing Call [Occupied]', 'Missing Call from ' . $client_phone_number .' to '.$incomingForm->call->calledPhone . "\r\n Reason: Agent Occupied", Notifications::TYPE_WARNING, true);
//                                //Notifications::socket($user->id, null, 'getNewNotification', [], true);
//                            }
//                        } else {
//                            // Yii::info('Call Status not Ready - User ('.$user->username.') Id: '.$user->id.', phone: ' . $incomingForm->call->calledPhone, 'info\API:CommunicationController:actionVoice:isCallStatusReady');
//                            // Notifications::create($user->id, 'Missing Call [not Ready]', 'Missing Call from ' . $client_phone_number .' to '.$incomingForm->call->calledPhone . "\r\n Reason: Call Status not Ready", Notifications::TYPE_WARNING, true);
//                            //Notifications::socket($user->id, null, 'getNewNotification', [], true);
//                        }
//                    } else {
//                        //Yii::info('Offline - User ('.$user->username.') Id: '.$user->id.', phone: ' . $incomingForm->call->calledPhone, 'info\API:CommunicationController:actionVoice:isOnline');
//                        //Notifications::create($user->id, 'Missing Call [Offline]', 'Missing Call from ' . $client_phone_number .' to '.$incomingForm->call->calledPhone . "\r\n Reason: Agent offline", Notifications::TYPE_WARNING, true);
//                        //Notifications::socket($user->id, null, 'getNewNotification', [], true);
//                    }
//                }
//                if (!$call_employee) {
//                    $isOnHold = true;
//                }
//            } else {
//                $isOnHold = true;
//                Yii::info('Call in Hold. phone: ' . $incomingCallForm->calledPhone, 'info\API:Communication:voiceIncoming:CallInHold');
//            }
//
//            if ($agentsInfo) {
//                Yii::info(VarDumper::dumpAsString($agentsInfo), 'info\API:Communication:voiceIncoming:isCallFree');
//            }
//
//        } elseif (!$source) {
//
//            $agentRes = $this->getDirectAgentsByPhoneNumber($incomingCallForm->calledPhone, $incomingCallForm->callerPhone, $generalLineSettings->direct_agent_user_limit);
//            if ($agentRes && isset($agentRes['call_employee'], $agentRes['call_agent_username']) && $agentRes['call_employee']) {
//                $isOnHold = false;
//                $callGeneralNumber = false;
//                $call_employee = $agentRes['call_employee'];
//                $call_project_id = $agentRes['call_project_id'] ?? null;
//                $call_agent_username = $agentRes['call_agent_username'];
//            } else {
//                if ($agentRes && isset($agentRes['call_project_id'])) {
//                    $call_project_id = ($agentRes['call_project_id'] > 0) ? $agentRes['call_project_id'] : null;
//                    if (NULL === $call_project_id) {
//                        $isOnHold = false;
//                        $callGeneralNumber = true;
//                    } else {
//                        $isOnHold = true;
//                        $callGeneralNumber = false;
//                    }
//                } else {
//                    $isOnHold = false;
//                    $callGeneralNumber = true;
//                }
//            }
//
//
//            if ($clientPhone) {
//                $lead2 = Lead2::findLastLeadByClientPhone($incomingCallForm->callerPhone, $agentRes['call_project_id'] ?? null);
//            }
//
//            if (!$lead2) {
//                //$sql = Lead2::findLastLeadByClientPhone($client_phone_number, true);
//                //Yii::info('phone: '. $client_phone_number.', sql: '. $sql, 'info\API:Communication:findLastLeadByClientPhone');
//                if (isset($agentRes['call_project_id']) && $agentRes['call_project_id']) {
//                    $lead2 = Lead2::createNewLeadByPhone($incomingCallForm->callerPhone, $agentRes['call_project_id']);
//                }
//            } /*else {
//                            Yii::info('Find LastLead ('.$lead2->id.') By ClientPhone: ' . $client_phone_number, 'info\API:Communication:findLastLeadByClientPhone');
//                        }*/
//
//
//        } else {
//            $callGeneralNumber = true;
//        }
//
//        // $clientPhone = ClientPhone::find()->where(['phone' => $client_phone_number])->orderBy(['id' => SORT_DESC])->limit(1)->one();
//        //$lead = null;
//
//        //if(!$lead) {
//        /*if ($clientPhone && $clientPhone->client_id) {
//            $lead = Lead::find()->select(['id'])->where(['client_id' => $clientPhone->client_id])->orderBy(['id' => SORT_DESC])->limit(1)->one();
//        }*/
//        //}
//
//        $data = [];
//        $data['client_name'] = 'Noname';
//        $data['client_id'] = null;
//        $data['last_lead_id'] = null;
//        $data['client_emails'] = [];
//        $data['client_phones'] = [];
//        $data['client_count_calls'] = 0;
//        $data['client_count_sms'] = 0;
//        $data['client_created_date'] = '';
//        $data['client_last_activity'] = '';
//
//        if ($clientPhone && $client = $clientPhone->client) {
//            $data['client_name'] = $client->full_name;
//            $data['client_id'] = $clientPhone->client_id;
//            $data['client_created_date'] = Yii::$app->formatter->asDate(strtotime($client->created));
//            if ($lead2) {
//                $data['last_lead_id'] = $lead2->id;
//                $data['client_last_activity'] = Yii::$app->formatter->asDate(strtotime($client->created));
//            }
//        }
//
//        $data['client_phone'] = $incomingCallForm->callerPhone;
//        $data['agent_phone'] = $incomingCallForm->calledPhone;
//
//
//        Yii::info(VarDumper::dumpAsString([
//            'data' => $data,
//            'post' => $post,
//            'call_employee' => $call_employee,
//
//        ], 10, false), 'info\API:Communication:voiceIncoming:ParamsToCall');
//
//        if (!$isOnHold && !$callGeneralNumber && $call_employee) {
//
//            foreach ($call_employee AS $key => $userCall) {
//                $call = new Call();
//                $call->c_call_sid = $post['call']['CallSid'] ?? null;
//                $call->c_call_type_id = Call::CALL_TYPE_IN;
//                $call->c_call_status = $post['call']['CallStatus'] ?? Call::CALL_STATUS_RINGING;
//                $call->c_com_call_id = $post['call_id'] ?? null;
//                $call->c_direction = $post['call']['Direction'] ?? null;
//                $call->c_project_id = $call_project_id;
//                $call->c_is_new = true;
//                $call->c_created_dt = date('Y-m-d H:i:s');
//                $call->c_from = $incomingCallForm->callerPhone;
//                $call->c_to = $incomingCallForm->calledPhone; //$userCall->username ? $userCall->username : null;
//                $call->c_created_user_id = $userCall->id;
//                $call->c_source_type_id = Call::SOURCE_REDIRECT_CALL;
//                if ($lead2) {
//                    $call->c_lead_id = $lead2->id;
//                } else {
//                    $call->c_lead_id = null;
//                }
//                if (!$call->save()) {
//                    Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceIncoming:Call:save');
//                }
//                $data['status'] = $call->c_call_status;
//                // Notifications::socket($call->c_created_user_id, $call->c_lead_id, 'incomingCall', $data, true);
//            }
//        } elseif ($isOnHold) {
//            $call = new Call();
//            $call->c_call_sid = $post['call']['CallSid'] ?? null;
//            $call->c_call_type_id = Call::CALL_TYPE_IN;
//            $call->c_call_status = Call::CALL_STATUS_QUEUE;
//            $call->c_com_call_id = $post['call_id'] ?? null;
//            $call->c_direction = $post['call']['Direction'] ?? null;
//            $call->c_project_id = $call_project_id;
//            $call->c_is_new = true;
//            $call->c_created_dt = date('Y-m-d H:i:s');
//            $call->c_from = $incomingCallForm->callerPhone;
//            $call->c_to = $incomingCallForm->calledPhone;
//            $call->c_created_user_id = null;
//            $call->c_source_type_id = $callSourceTypeId;
//            if ($lead2) {
//                $call->c_lead_id = $lead2->id;
//            }
//            if (!$call->save()) {
//                Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceIncoming:save:isOnHold');
//            }
//
//            if ($call_project_id) {
//                $project = Project::findOne($call_project_id);
//            } else {
//                $project = null;
//            }
//
//            $responseTwml = new VoiceResponse();
//
//            $url_say_play_hold = '';
//            $url_music_play_hold = 'https://talkdeskapp.s3.amazonaws.com/production/audio_messages/folk_hold_music.mp3';
//
//            if ($project && $project->custom_data) {
//                $customData = @json_decode($project->custom_data, true);
//                if ($customData) {
//                    if (isset($customData['url_say_play_hold']) && $customData['url_say_play_hold']) {
//                        $url_say_play_hold = $customData['url_say_play_hold'];
//                    }
//
//                    if (isset($customData['url_music_play_hold']) && $customData['url_music_play_hold']) {
//                        $url_music_play_hold = $customData['url_music_play_hold'];
//                    }
//                }
//            }
//
//            if ($url_say_play_hold) {
//                $responseTwml->play($url_say_play_hold);
//                if ($url_music_play_hold) {
//                    $responseTwml->play($url_music_play_hold);
//                }
//
//            } else {
//
//
//                $say_params = \Yii::$app->params['voice_gather'];
//                $responseTwml = new VoiceResponse();
//                $responseTwml->pause(['length' => 5]);
//
//                $company = ' ' . strtolower($project->name);
//                $entry_phrase = str_replace('{{project}}', $company, $say_params['entry_phrase']);
//                $responseTwml->say('    ' . $entry_phrase . '  ' . $say_params['languages'][1]['hold_voice'], [
//                    'language' => $say_params['languages'][1]['language'],
//                    'voice' => $say_params['languages'][1]['voice'],
//                ]);
//                $responseTwml->play($say_params['hold_play']);
//                $response['twml'] = (string)$responseTwml;
//            }
//
//            $response['twml'] = (string)$responseTwml;
//
//            Yii::info('Call add to hold : call_project_id: ' . $call_project_id . ', generalLine: ' . $generalLineNumber . ', TWML: ' . $response['twml'], 'info\API:Communication:voiceIncoming:isOnHold - 5');
//
//        } elseif ($callGeneralNumber) {
//            $call = new Call();
//            $call->c_call_sid = $post['call']['CallSid'] ?? null;
//            $call->c_call_type_id = Call::CALL_TYPE_IN;
//            $call->c_call_status = $post['call']['CallStatus'] ?? Call::CALL_STATUS_RINGING;
//            $call->c_com_call_id = $post['call_id'] ?? null;
//            $call->c_direction = $post['call']['Direction'] ?? null;
//            $call->c_project_id = $call_project_id;
//            $call->c_is_new = true;
//            $call->c_created_dt = date('Y-m-d H:i:s');
//            $call->c_from = $incomingCallForm->callerPhone;
//            $call->c_to = $generalLineNumber;
//            $call->c_created_user_id = null;
//            $call->c_source_type_id = $callSourceTypeId;
//            if ($lead2) {
//                $call->c_lead_id = $lead2->id;
//            }
//            if (!$call->save()) {
//                Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceIncoming:Call:save:callGeneralNumber');
//            }
//            Yii::info('Redirected to General Line : call_project_id: ' . $call_project_id . ', generalLine: ' . $generalLineNumber, 'info\API:Communication:voiceIncoming:callGeneralNumber - 6');
//        } else {
//            if (!$isOnHold && !$callGeneralNumber) {
//                $isError = true;
//                Yii::error('Not found call destination agent, hold or general line for call number:' . $incomingCallForm->calledPhone, 'API:Communication:voiceIncoming:isOnHold_callGeneralNumber');
//            }
//        }
//
//        if (!$isError) {
//            $response['agent_sip'] = '';
//            $response['agent_phone_number'] = $incomingCallForm->calledPhone;
//            $response['client_phone_number'] = $incomingCallForm->callerPhone;
//            $response['general_phone_number'] = $generalLineNumber;
//            $response['agent_username'] = $call_agent_username;
//            $response['call_to_hold'] = $isOnHold ? 1 : 0;
//            $response['call_to_general'] = $callGeneralNumber ? 1 : 0;
//        } else {
//            $response['error'] = 'Not found call destination agent, hold or general line';
//            $response['error_code'] = 13;
//        }
//
//        return $response;
//    }
//
    protected
    function getDirectAgentsByPhoneNumber(string $agent_phone_number, string $client_phone_number, int $limit = 10): array
    {
        $call_employee = [];
        $call_agent_username = [];
        $upp = UserProjectParams::find()->where(['upp_tw_phone_number' => $agent_phone_number])->one();
        $user = null;
        $call_user_id = null;
        $call_project_id = null;

        if ($upp && $user = $upp->uppUser) {
            $call_user_id = (int)$upp->upp_user_id;
            $call_project_id = (int)$upp->upp_project_id;

            if ($user->isOnline()) {
                if ($user->isCallStatusReady()) {
                    if ($user->isCallFree()) {
                        Yii::info('DIRECT - User (' . $user->username . ') Id: ' . $user->id . ', phone: ' . $agent_phone_number, 'info\API:CommunicationController:actionVoice:Direct - 2');
                        if ($user->userProfile && (int)$user->userProfile->up_call_type_id === UserProfile::CALL_TYPE_WEB) {
                            $call_employee[] = $user;
                            $call_agent_username[] = 'seller' . $user->id;
                        }

                    } else {
                        Yii::info('Call Occupied - User (' . $user->username . ') Id: ' . $user->id . ', phone: ' . $agent_phone_number, 'info\API:CommunicationController:actionVoice:isCallFree');
                        Notifications::create($user->id, 'Missing Call [Occupied]', 'Missing Call from ' . $client_phone_number . ' to ' . $agent_phone_number . "\r\n Reason: Agent Occupied", Notifications::TYPE_WARNING, true);
                        Notifications::socket($user->id, null, 'getNewNotification', [], true);
                    }
                } else {
                    Yii::info('Call Status not Ready - User (' . $user->username . ') Id: ' . $user->id . ', phone: ' . $agent_phone_number, 'info\API:CommunicationController:actionVoice:isCallStatusReady');
                    Notifications::create($user->id, 'Missing Call [not Ready]', 'Missing Call from ' . $client_phone_number . ' to ' . $agent_phone_number . "\r\n Reason: Call Status not Ready", Notifications::TYPE_WARNING, true);
                    Notifications::socket($user->id, null, 'getNewNotification', [], true);
                }
            } else {
                Yii::info('Offline - User (' . $user->username . ') Id: ' . $user->id . ', phone: ' . $agent_phone_number, 'info\API:CommunicationController:actionVoice:isOnline');
                Notifications::create($user->id, 'Missing Call [Offline]', 'Missing Call from ' . $client_phone_number . ' to ' . $agent_phone_number . "\r\n Reason: Agent offline", Notifications::TYPE_WARNING, true);
                Notifications::socket($user->id, null, 'getNewNotification', [], true);
            }
        }

        if (!$call_employee && $call_user_id && $call_project_id) {

            Yii::info('isRedirectCall - call_user_id (' . $call_user_id . '), call_project_id: ' . $call_project_id, 'info\API:CommunicationController:actionVoice:Redirect - 3');
            $usersForCall = Employee::getAgentsForCall($call_user_id, $call_project_id);

            Yii::info('Redirect usersForCall: ' . VarDumper::dumpAsString($usersForCall), 'info\API:CommunicationController:actionVoice:getAgentsForCall - 4');

            if ($usersForCall) {
                $cntCallAgents = 1;
                foreach ($usersForCall as $userForCall) {
                    $upp = UserProjectParams::find()->where(['upp_user_id' => $userForCall['tbl_user_id'], 'upp_project_id' => $call_project_id])->one();
                    if ($upp) {
                        $employeeModel = Employee::findOne(['id' => $userForCall['tbl_user_id']]);
                        if ($employeeModel && $employeeModel->userProfile && (int)$employeeModel->userProfile->up_call_type_id === UserProfile::CALL_TYPE_WEB) {
                            if ($cntCallAgents > $limit) {
                                break;
                            }
                            $call_employee[] = $employeeModel;
                            $call_agent_username[] = 'seller' . $employeeModel->id;
                            Yii::info('Redirected Call: call_user_id: ' . $call_user_id . ', call: ' . 'seller' . $employeeModel->id . ', agent_phone_number: ' . $agent_phone_number, 'info\API:CommunicationController:actionVoice:UserProjectParams - 5');
                            //break;
                            $cntCallAgents++;
                        }
                    }
                }
            }
        }

        $result = [
            'call_employee' => $call_employee,
            'call_project_id' => $call_project_id,
            'call_agent_username' => $call_agent_username,
        ];

        return $result;
    }


}