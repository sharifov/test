<?php
namespace webapi\src\services\communication;

use common\components\jobs\CallQueueJob;
use common\models\ApiLog;
use common\models\Call;
use common\models\CallUserGroup;
use common\models\ClientPhone;
use common\models\Conference;
use common\models\ConferenceParticipant;
use common\models\ConferenceRoom;
use common\models\Department;
use common\models\DepartmentPhoneProject;
use common\models\Employee;
use common\models\Lead;
use common\models\Notifications;
use common\models\Sources;
use common\models\UserProjectParams;
use frontend\widgets\notification\NotificationMessage;
use sales\entities\cases\Cases;
use sales\model\phoneList\entity\PhoneList;
use sales\repositories\lead\LeadRepository;
use sales\services\call\CallDeclinedException;
use sales\services\call\CallService;
use Twilio\TwiML\VoiceResponse;
use webapi\src\repositories\communication\CommunicationRepository;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * Class CommunicationService
 * @package webapi\src\services\communication
 *
 * @property CallService $callService
 * @property CommunicationRepository $communicationRepository
 */
class CommunicationService
{
	/**
	 * @var CallService
	 */
	private CallService $callService;

	/**
	 * @var CommunicationRepository
	 */
	private CommunicationRepository $communicationRepository;

	/**
	 * CommunicationService constructor.
	 * @param CallService $callService
	 * @param CommunicationRepository $communicationRepository
	 */
	public function __construct(CallService $callService, CommunicationRepository $communicationRepository)
	{
		$this->callService = $callService;
		$this->communicationRepository = $communicationRepository;
	}

	public function voiceIncoming(RequestDataDTO $requestDataDTO): array
	{
		$response = [];
		$clientPhone = null;

		$client_phone_number = null;
		$incoming_phone_number = null;

		$callSid = $requestDataDTO->CallSid ?? null;
		$parentCallSid = $requestDataDTO->ParentCallSid ?? null;
		$client_phone_number = $requestDataDTO->From ?? null;
		$incoming_phone_number = $requestDataDTO->Called ?? null;

		if (!$client_phone_number) {
			$response['error'] = 'Not found Call From (Client phone number)';
			$response['error_code'] = 10;
		}

		if (!$incoming_phone_number) {
			$response['error'] = 'Not found Call Called (Agent phone number)';
			$response['error_code'] = 11;
		}

		try {
			$this->callService->guardDeclined($client_phone_number, ArrayHelper::toArray($requestDataDTO), Call::CALL_TYPE_IN);
		} catch (CallDeclinedException $e) {
			$vr = new VoiceResponse();
			$vr->reject(['reason' => 'busy']);
			return $this->getResponseChownData($vr, 404, 404, 'Sales Communication error: '. $e->getMessage());
		} catch (\RuntimeException $e) {
			throw new $e;
		}

		$conferenceRoom = ConferenceRoom::find()->where(['cr_phone_number' => $incoming_phone_number, 'cr_enabled' => true])->orderBy(['cr_id' => SORT_DESC])->limit(1)->one();
		if ($conferenceRoom) {
			return $this->startConference($conferenceRoom, $requestDataDTO);
		}

		$phone = PhoneList::find()->byPhone($incoming_phone_number)->enabled()->limit(1)->one();
		if ($phone && $departmentPhone = $phone->departmentPhoneProject) {
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
			$call_source_id = $departmentPhone->dpp_source_id;

			$ivrEnable = (bool)$departmentPhone->dpp_ivr_enable;

			$callModel = $this->communicationRepository->findOrCreateCall(
				$callSid,
				$parentCallSid,
				$requestDataDTO,
				$call_project_id,
				$call_dep_id
			);

			if ($departmentPhone->dugUgs) {
				foreach ($departmentPhone->dugUgs as $userGroup) {
					$exist = CallUserGroup::find()->where([ 'cug_ug_id' => $userGroup->ug_id, 'cug_c_id' => $callModel->c_id])->exists();
					if ($exist) {
						continue;
					}
					$cug = new CallUserGroup();
					$cug->cug_ug_id = $userGroup->ug_id;
					$cug->cug_c_id = $callModel->c_id;
					if (!$cug->save()) {
						Yii::error(VarDumper::dumpAsString($cug->errors),
							'API:Communication:voiceIncoming:CallUserGroup:save');
					}
				}
			}
			$callModel->c_source_type_id = Call::SOURCE_GENERAL_LINE;

			if ($ivrEnable) {
				$ivrStep = (int)Yii::$app->request->get('step', 1);
				return $this->ivrService($callModel, $departmentPhone, $ivrStep, $requestDataDTO->Digits);
			}

			$response['error'] = 'Not enable IVR';
			$response['error_code'] = 13;

		} else {
			$upp = UserProjectParams::find()->byPhone($incoming_phone_number, false)->limit(1)->one();
			if ($upp) {

				if ($upp->upp_dep_id) {
					$call_dep_id = $upp->upp_dep_id;
				} elseif ($upp->uppUser && $upp->uppUser->userDepartments && isset($upp->uppUser->userDepartments[0])) {
					$call_dep_id = $upp->uppUser->userDepartments[0]->ud_dep_id;

				} else {
					$call_dep_id = null;
				}

				$callModel = $this->communicationRepository->findOrCreateCall(
					$callSid,
					$parentCallSid,
					$requestDataDTO,
					$upp->upp_project_id,
					$call_dep_id
				);
				$callModel->c_source_type_id = Call::SOURCE_DIRECT_CALL;

				$user = $upp->uppUser;

				if ($user) {
					if ($user->isOnline()) {
						// Yii::info('DIRECT CALL - User (' . $user->username . ') Id: ' . $user->id . ', phone: ' . $incoming_phone_number, 'info\API:Communication:Incoming:DirectCall');
						return $this->createDirectCall($callModel, $user);
					}

					Yii::info('Offline - User (' . $user->username . ') Id: ' . $user->id . ', phone: ' . $incoming_phone_number,
						'info\API:Communication:Incoming:Offline');
					if ($ntf = Notifications::create($user->id, 'Missing Call [Offline]',
						'Missing Call from ' . $client_phone_number . ' to ' . $incoming_phone_number . "\r\n Reason: Agent offline",
						Notifications::TYPE_WARNING, true)
					) {
						$dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
						Notifications::sendSocket('getNewNotification', ['user_id' => $user->id], $dataNotification);
					}
					$callModel->c_source_type_id = Call::SOURCE_REDIRECT_CALL;
					return $this->createHoldCall($callModel, $user);
				}

				$response['error'] = 'Not found "user" for Call';
				$response['error_code'] = 14;

			}

		}

		return $this->createExceptionCall($incoming_phone_number); //$ciscoPhoneNumber


//		$response['error'] = 'Not found "call" data';
//		$response['error_code'] = 12;
//		return $response;
	}

	public function voiceRecord(array $post = []): array
	{
		$response = [];
		$callData = $post['callData'] ?? [];

		if ($callData && isset($callData['CallSid'], $callData['RecordingSid']) ) {
			$call = Call::find()->where(['c_recording_sid' => $callData['RecordingSid']])->limit(1)->one();

			if (!$call) {
				$call = Call::find()->where(['c_call_sid' => $callData['CallSid']])->orderBy(['c_id' => SORT_ASC])->limit(1)->one();
			}

			if ($call->isGeneralParent()) {
				$call = Call::find()->firstChild($call->c_id)->one();
			}

			if ($call && $callData['RecordingUrl']) {

				if (!$call->c_recording_sid && $callData['RecordingSid']) {
					$call->c_recording_sid = $callData['RecordingSid'];
				}

				$call->c_recording_duration = $callData['RecordingDuration'] ?? null;

				if(!$call->save()) {
					Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceRecord:Call:save');
				}
			}
		} else {
			$response['error'] = 'Not found callData[CallSid] or callData[RecordingSid] in voiceRecord';
		}

		return $response;
	}

	/**
	 * @param ConferenceRoom $conferenceRoom
	 * @param RequestDataDTO $postCall
	 * @return array
	 */
	private function startConference(ConferenceRoom $conferenceRoom, RequestDataDTO $postCall): array
	{
		$vr = new VoiceResponse();
		try {
			$call = $this->findOrCreateCallByData($postCall);
			$call->c_source_type_id = Call::SOURCE_CONFERENCE_CALL;
			if (!$call->save()) {
				Yii::error(VarDumper::dumpAsString($call->errors), 'API:CommunicationController:startConference:Call:save');
			}

			$sayParam = ['language' => 'en-US'];   // ['language' => 'en-US', 'voice' => 'alice']

			if ($conferenceRoom->cr_start_dt && strtotime($conferenceRoom->cr_start_dt) > time() ) {
				$vr->say('This conference room has not started yet', $sayParam);
				$vr->reject(['reason' => 'busy']);
				Yii::warning('Conference (id: ' . $conferenceRoom->cr_id . ') has not started yet', 'API:CommunicationController:startConference:start');
				return $this->getResponseChownData($vr);
			}

			if ($conferenceRoom->cr_end_dt && strtotime($conferenceRoom->cr_end_dt) < time() ) {
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

			$dial = $vr->dial('');
			$params = $conferenceRoom->getCreatedTwParams();

			$dial->conference($conferenceRoom->cr_key, $params);

		} catch (\Throwable $e) {

			$vr->say('Conference Error!');
			$vr->reject(['reason' => 'busy']);
			return $this->getResponseChownData($vr, 404, 404, 'Sales Communication error: '. $e->getMessage(). "\n" . $e->getFile() . ':' . $e->getLine());
		}

		return $this->getResponseChownData($vr);
	}

	public function voiceConferenceCallback(array $post = []): array
	{
		$response = [];

		if (isset($post['conferenceData']['ConferenceSid']) && $post['conferenceData']['ConferenceSid']) {

			$conferenceData = $post['conferenceData'];
			$conferenceSid = mb_substr($conferenceData['ConferenceSid'], 0, 34);

			$conference = Conference::findOne(['cf_sid' => $conferenceSid]);

			if (!$conference) {

				$conferenceRoom = ConferenceRoom::find()->where(['cr_key' => $conferenceData['FriendlyName'], 'cr_enabled' => true])->limit(1)->one();

				if ($conferenceRoom) {
					$conference = new Conference();
					$conference->cf_cr_id = $conferenceRoom->cr_id;
					$conference->cf_options = @json_encode($conferenceRoom->attributes);
					$conference->cf_status_id = Conference::STATUS_START;
					$conference->cf_sid = $conferenceSid;
					if (!$conference->save()) {
						Yii::error(VarDumper::dumpAsString($conference->errors),
							'API:CommunicationController:startConference:Conference:save');
					}
				} else {
					Yii::warning('Not found ConferenceRoom by key: conferenceData - ' . VarDumper::dumpAsString($conferenceData),
						'API:CommunicationController:startConference:conferenceData:notfound');
				}
			}


			if ($conference) {

				if ($conferenceData['StatusCallbackEvent'] === 'conference-end') {
					$conference->cf_status_id = Conference::STATUS_END;
					if (!$conference->save()) {
						Yii::error(VarDumper::dumpAsString($conference->errors),
							'API:CommunicationController:startConference:Conference:save-end');
					}
				} elseif ($conferenceData['StatusCallbackEvent'] === 'participant-join') {

					$call = Call::find()->where(['c_call_sid' => $conferenceData['CallSid']])->one();

					$cPart = new ConferenceParticipant();
					$cPart->cp_cf_id = $conference->cf_id;
					$cPart->cp_call_sid = $conferenceData['CallSid'];
					if ($call) {
						$cPart->cp_call_id = $call->c_id;
					}
					$cPart->cp_status_id = ConferenceParticipant::STATUS_JOIN;
					$cPart->cp_join_dt = date('Y-m-d H:i:s');
					if(!$cPart->save()) {
						Yii::error(VarDumper::dumpAsString($cPart->errors), 'API:Communication:voiceConferenceCallback:ConferenceParticipant:save-join');
					}

				} elseif ($conferenceData['StatusCallbackEvent'] === 'participant-leave') {
					$cPart = ConferenceParticipant::find()->where(['cp_call_sid' => $conferenceData['CallSid']])->one();

					if ($cPart) {
						$cPart->cp_status_id = ConferenceParticipant::STATUS_LEAVE;
						$cPart->cp_leave_dt = date('Y-m-d H:i:s');
						if (!$cPart->save()) {
							Yii::error(VarDumper::dumpAsString($cPart->errors),
								'API:Communication:voiceConferenceCallback:ConferenceParticipant:save-leave');
						}
					} else {
						Yii::warning('Not found ConferenceParticipant by callSid: conferenceData - ' . VarDumper::dumpAsString($conferenceData),
							'API:CommunicationController:voiceConferenceCallback:conferenceData:notfound');
					}
				}
			}

		} else {
			$response['error'] = 'Not found POST[conferenceData][ConferenceSid]';
			Yii::error($response['error'] . ' - ' . VarDumper::dumpAsString($post), 'API:Communication:voiceConferenceCallback:notFound');
		}

		return $response;
	}

	public function voiceConferenceRecordCallback(array $post = []): array
	{
		$response = [];

		if (isset($post['conferenceData']['ConferenceSid']) && $post['conferenceData']['ConferenceSid']) {

			$conferenceData = $post['conferenceData'];
			$conferenceSid = mb_substr($conferenceData['ConferenceSid'], 0, 34);

			$conference = Conference::findOne(['cf_sid' => $conferenceSid]);

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

		} else {
			$response['error'] = 'Not found POST[conferenceData][ConferenceSid]';
			Yii::error($response['error'] . ' - ' . VarDumper::dumpAsString($post), 'API:Communication:voiceConferenceRecordCallback:notFoundData');
		}

		return $response;
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

		try {
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
					$job->source_id = $department->dpp_source_id;
					$job->delay = 0;
					$jobId = Yii::$app->queue_job->delay(7)->priority(80)->push($job);
				}

				if(isset($ivrParams['hold_play']) && $ivrParams['hold_play']) {
					$responseTwml->play($ivrParams['hold_play'], ['loop' => 0]);
				}

			}

//			$response['twml'] = (string) $responseTwml;
//			$responseData = [
//				'status' => 200,
//				'name' => 'Success',
//				'code' => 0,
//				'message' => ''
//			];
//			$responseData['data']['response'] = $response;
			$responseData = $this->getResponseChownData($responseTwml, 200, 0);
		} catch (\Throwable $e) {
			$responseTwml = new VoiceResponse();
			$responseTwml->reject(['reason' => 'busy']);
			$responseData = $this->getResponseChownData($responseTwml, 404, 404, 'Sales Communication error: '. $e->getMessage(). "\n" . $e->getFile() . ':' . $e->getLine());
		}
		return $responseData;
	}

	/**
	 * @param RequestDataDTO $requestDataDTO
	 * @return Call
	 */
	protected function findOrCreateCallByData(RequestDataDTO $requestDataDTO): Call
	{
		$call = null;
		$parentCall = null;

		$callSid = $requestDataDTO->CallSid;
		$parentCallSid = $requestDataDTO->ParentCallSid ?? '';

		if ($callSid) {
			$call = Call::find()->where(['c_call_sid' => $callSid])->limit(1)->one();
			if ($call && $call->isDeclined()) {
				$call->c_call_status = $requestDataDTO->CallStatus;
				return $call;
			}
		}

		if ($parentCallSid) {
			$parentCall = Call::find()->where(['c_call_sid' => $parentCallSid])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();
		}


		if (!$call) {

			$call = new Call();
			$call->c_call_sid = $requestDataDTO->CallSid ?? null;
			$call->c_parent_call_sid = $requestDataDTO->ParentCallSid ?? null;
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
			}

			$call->c_is_new = true;
			$call->c_created_dt = date('Y-m-d H:i:s');
			$call->c_from = $requestDataDTO->From;
			$call->c_to = $requestDataDTO->To; //Called
			$call->c_created_user_id = null;
		}
		$call->c_call_status = $requestDataDTO->CallStatus;
		$call->setStatusByTwilioStatus($call->c_call_status);


		$agentId = null;

		if (!empty($requestDataDTO->Called)) {
			if (strpos($requestDataDTO->Called, 'client:seller') !== false) {
				$agentId = (int)str_replace('client:seller', '', $requestDataDTO->Called);
			}
		}

		if (!$agentId) {
			if (!empty($requestDataDTO->c_user_id)) {
				$agentId = $requestDataDTO->c_user_id;
			}
		}

		if ($agentId) {
			$call->c_created_user_id = $agentId;
		}

		if (!$call->c_created_user_id && $parentCall && $call->isOut()) {
			$call->c_created_user_id = $parentCall->c_created_user_id;
		}

		$call->c_sequence_number = $requestDataDTO->SequenceNumber;
		$call->c_call_duration = $requestDataDTO->CallDuration;
		$call->c_forwarded_from = $requestDataDTO->ForwardedFrom;

		if (!$call->c_recording_sid && !empty($requestDataDTO->RecordingSid)) {
			$call->c_recording_sid = $requestDataDTO->RecordingSid;
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
	private function createExceptionCall(string $phoneNumber): array
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

	/**
	 * @param array $response
	 * @param ApiLog $apiLog
	 * @return array
	 */
	public function getResponseData(array $response, ApiLog $apiLog): array
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
		$responseData = $apiLog->endApiLog($responseData);

		return $responseData;
	}

	/**
	 * @param RequestDataDTO $requestDataDTO
	 * @param $apiLog
	 * @return VoiceResponse
	 */
	public function callFromJwtClient(RequestDataDTO $requestDataDTO, $apiLog): VoiceResponse
	{
		$response = new VoiceResponse();
		$responseData = [];
		try {

			$number_or_client = $requestDataDTO->To;
			if(!$number_or_client) {
				throw new \RuntimeException('Phone or client not found. ' . $number_or_client);
			}

			$phone_number = PhoneList::findOne(['pl_phone_number' => $requestDataDTO->FromAgentPhone]);
			if(!$phone_number) {
				$responseData['error_code'] = 25;
				throw new \RuntimeException('Phone number not found. ' . $requestDataDTO->FromAgentPhone);
			}

			$projectId = $requestData['project_id'] ?? null;
			if(NULL === $projectId) {
				$phoneNumberProject = DepartmentPhoneProject::findOne(['dpp_phone_list_id' => $phone_number->pl_id]);
				if ($phoneNumberProject) {
					$projectId = $phoneNumberProject->dppProject->id;
				}
			}

			$callModel = new Call();
			$callModel->c_project_id = $projectId;
			$callModel->c_call_type_id = Call::CALL_TYPE_OUT;
			$callModel->c_call_sid = $requestDataDTO->CallSid;
			$callModel->c_parent_call_sid = $requestDataDTO->ParentCallSid ?? null;
			$callModel->c_to = $requestDataDTO->To;
			$callModel->c_from = $requestDataDTO->From;
			$callModel->c_call_status = $requestDataDTO->CallStatus;
			$callModel->c_caller_name = $requestDataDTO->Caller ?? null;
			$callModel->c_created_dt = date('Y-m-d H:i:s');

			if(!$callModel->save()) {
				Yii::error(VarDumper::dumpAsString($callModel->errors, 10), 'API:TwilioController:callFromJwtClient:Call:save');
				throw new \RuntimeException('Error save call model');
			}

			if (preg_match("/^[\+0-9\-\(\)\s]*$/", $number_or_client)) {
				if($number_or_client !== $phone_number->pl_phone_number) {
					$dial = $response->dial('', [
						'recordingStatusCallbackMethod' => 'POST',
						'callerId' => $phone_number->pl_phone_number,
						'record' => 'record-from-answer-dual',
						'recordingStatusCallback' => $this->recordingStatusCallbackUrl
					]);
					$dial->number($number_or_client, [
						'statusCallbackEvent' => 'ringing answered completed',
						'statusCallback' => $this->voiceStatusCallbackUrl,
						'statusCallbackMethod' => 'POST',
					]);
				} else {
					throw new \RuntimeException('To And From is Same');
				}
			} else {
				$dial = $response->dial('', [
					'recordingStatusCallbackMethod' => 'POST',
					//'callerId' => $requestData['From'],
					'record' => 'record-from-answer-dual',
					'recordingStatusCallback' => $this->recordingStatusCallbackUrl
				]);
				$dial->client($number_or_client, [
					'statusCallbackEvent' => 'ringing answered completed',
					'statusCallback' => $this->voiceStatusCallbackUrl,
					'statusCallbackMethod' => 'POST',
				]);
			}

			$this->voiceClient($requestDataDTO);
		} catch (\Throwable $e) {
			$response =  new VoiceResponse();
			$response->reject(['reason' => 'busy']);

			$responseData['error'] = $e->getMessage();
			if(!isset($responseData['error_code']) || !$responseData['error_code']) {
				$responseData['error_code'] = 20;
			}
			$responseData['message'] =  $e->getMessage().' (code:'.$e->getCode().', line: '.$e->getLine().')';
			\Yii::error($responseData['message'], 'API:TwilioController:callFromJwtClient:Throwable');
		}

		$apiLog->endApiLog($responseData);
		return $response;
	}

	public function voiceClient(RequestDataDTO $requestDataDTO): array
	{
		$response = [];

		$callSid = $requestDataDTO->callData['sid'] ?? $requestDataDTO->callData['CallSid'] ?? null;

		if ($callSid) {

			$call = Call::find()->where(['c_call_sid' => $callSid])->orderBy(['c_id' => SORT_ASC])->limit(1)->one();

			$callData = $requestDataDTO->call;
			$callOriginalData = $requestDataDTO->callData;

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

					if (
						isset($callOriginalData['c_source_type_id'])
						&& $callOriginalData['c_source_type_id']
						&& (int)$callOriginalData['c_source_type_id'] === Call::SOURCE_REDIAL_CALL
					) {
						$call->c_source_type_id = Call::SOURCE_REDIAL_CALL;
					}

					if (!$call->c_client_id && $call->c_to) {
						$clientPhone = ClientPhone::find()->where(['phone' => $call->c_to])->orderBy(['id' => SORT_DESC])->limit(1)->one();
						if ($clientPhone && $clientPhone->client_id) {
							$call->c_client_id = $clientPhone->client_id;
						}
					}

					if (isset($callOriginalData['lead_id']) && $callOriginalData['lead_id'] && ($lead = Lead::findOne((int)$callOriginalData['lead_id']))) {
						$call->c_dep_id = $lead->l_dep_id;
					} elseif (isset($callOriginalData['case_id']) && $callOriginalData['case_id'] && ($case = Cases::findOne((int)$callOriginalData['case_id']))) {
						$call->c_dep_id = $case->cs_dep_id;
					} elseif (!$call->c_dep_id && $call->c_project_id && isset($callOriginalData['FromAgentPhone']) && $callOriginalData['FromAgentPhone']) {
						$upp = UserProjectParams::find()->byPhone($callOriginalData['FromAgentPhone'], false)->andWhere(['upp_project_id' => $call->c_project_id])->limit(1)->one();
						if ($upp && $upp->upp_dep_id) {
							$call->c_dep_id = $upp->upp_dep_id;
						}
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
	 * @throws \RuntimeException
	 */
	public function voiceDefault(array $post = []): array
	{
		$response = [
			'trace' => [],
			'error' => '',
		];

		if (isset($post['callData']['CallSid']) && $post['callData']['CallSid']) {

			$callData = $post['callData'];
			$call = $this->findOrCreateCallByData($callData);

			if($call->isStatusNoAnswer() || $call->isStatusBusy() || $call->isStatusCanceled() || $call->isStatusFailed()) {
				if ($call->c_lead_id) {
					if (($lead = $call->cLead) && !$lead->isCallCancel()) {
						try {
							$leadRepository = Yii::createObject(LeadRepository::class);
							$lead->callCancel();
							$leadRepository->save($lead);
						} catch (\Throwable $e) {
							Yii::error('LeadId: ' . $lead->id . ' Message: ' . $e->getMessage() ,'API:Communication:voiceDefault:Lead:save');
							$response['error'] = 'Error in method voiceDefault. LeadId: ' . $lead->id . ' Message: ' . $e->getMessage();
						}
					}
				}

			}

			if (!$call->save()) {
				Yii::error(VarDumper::dumpAsString($call->errors), 'API:Communication:voiceDefault:Call:save');
				$response['error'] = 'Error in method voiceDefault. ' . $call->getErrorSummary(false)[0];
			}

		} else {
			Yii::error('Not found POST[callData][CallSid] ' . VarDumper::dumpAsString($post), 'API:Communication:voiceDefault:callData:notFound');
			$response['error'] = 'Error in method voiceDefault. Not found POST[callData][CallSid]';
		}

		$response['status'] = $response['error'] !== '' ? 'Fail' : 'Success';

		return $response;
	}

	private function getResponseChownData(VoiceResponse $vr, int $status = 200, int $code = 0, string $message = ''): array
	{
		$response['twml'] = (string) $vr;
		return [
			'status' => $status,
			'name' => ($status === 200 ? 'Success' : 'Error'),
			'code' => $code,
			'message' => $message,
			'data' => ['response' => $response]
		];
	}
}