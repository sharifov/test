<?php
namespace modules\twilio\components;

use common\components\CommunicationServiceInterface;
use common\models\Sms;
use modules\twilio\src\entities\SmsForm;
use modules\twilio\src\entities\ApiVoice;
use modules\twilio\src\entities\twilioJwtToken\TwilioJwtToken;
use modules\twilio\src\repositories\sms\SmsRepository;
use modules\twilio\src\services\sms\SmsCommunicationService;
use sales\helpers\app\AppHelper;
use Twilio\Jwt\ClientToken;
use Twilio\Rest\Client as TwClient;
use Yii;
use yii\helpers\VarDumper;

/**
 * Class TwilioCommunicationService
 * @package modules\twilio\components
 *
 * @property string $host
 * @property array $twilio_configs
 * @property SmsCommunicationService $smsCommunicationService
 * @property SmsRepository $smsRepository
 *
 * @see CommunicationServiceInterface
 */
class TwilioCommunicationService extends \common\components\CommunicationService
{
	public $host;

	public array $twilio_configs;
	/**
	 * @var SmsCommunicationService
	 */
	private SmsCommunicationService $smsCommunicationService;
	/**
	 * @var SmsRepository
	 */
	private SmsRepository $smsRepository;

	public function __construct(SmsCommunicationService $smsCommunicationService, SmsRepository $smsRepository, $config = [])
	{
		parent::__construct($config);
		$this->smsCommunicationService = $smsCommunicationService;
		$this->smsRepository = $smsRepository;
	}

	public function init(): void
	{
		$this->twilio_configs = [
			'account_sid' => Yii::$app->twilio->account_sid,
			'auth_token' => Yii::$app->twilio->auth_token,
			'app_sid' => Yii::$app->twilio->app_sid,
		];

		parent::init();
	}

	public function smsPreview(int $project_id, ?string $template_type, string $phone_from, string $phone_to, array $sms_data = [], ?string $language = 'en-US'): array
	{
		return [
			'data' => [
				'sms_text' => ''
			]
		];
	}

	public function smsSend(int $project_id, ?string $template_type, string $phone_from, string $phone_to, array $content_data = [], array $sms_data = [], ?string $language = 'en-US', ?int $delay = 0): array
	{
		$out = ['error' => false, 'data' => []];

		$data['sms']['sq_phone_from'] = $phone_from;
		$data['sms']['sq_phone_to'] = $phone_to;
		$data['sms']['sq_type_key'] = $template_type;
		$data['sms']['sq_language_id'] = $language;
		$data['sms']['sq_sms_data'] = $sms_data;

		if(isset($content_data['sms_text'])) {
			$data['sms']['sq_sms_text'] = $content_data['sms_text'];
		}

		if($delay > 0) {
			$data['sms']['sq_delay'] = $delay;
		}

		$smsForm = new SmsForm();
		$smsForm->sq_project_id = $project_id;
		$smsForm->s_id = $sms_data['s_id'] ?? null;

		try {
			if($smsForm->load($data) && !$smsForm->validate()) {
				if ($errors = $smsForm->getErrorSummary(false)[0]) {
					throw new \RuntimeException($errors, 2);
				}
				throw new \RuntimeException('Not validate SMS data', 3);
			}

			if (!$response = $this->smsCommunicationService->sendSms($smsForm, $delay)) {
				$out['error'] = 'Not save in Queue Job';
				return $out;
			}
		} catch (\Throwable $e) {
			$out['error'] = $e->getMessage();
			Yii::error(AppHelper::throwableFormatter($e), 'TwilioModule::TwilioCommunicationService::smsSend::Throwable');
			return $out;
		}

		$response['sq_id'] = null;
		$response['sq_project_id'] = $project_id;
		$response['sq_phone_from'] = $smsForm->sq_phone_from;
		$response['sq_phone_to'] = $smsForm->sq_phone_to;
		$response['sq_sms_text'] = $smsForm->sq_sms_text;
		$response['sq_sms_data'] = @json_encode($sms_data);
		$response['sq_type_id'] = null;
		$response['sq_language_id'] = $smsForm->sq_language_id;
		$response['sq_priority'] = Sms::PRIORITY_NORMAL;
		$response['sq_delay'] = $delay;

		if(isset($response['error']) && $response['error']) {
			$out['error'] = $response['error'];
		} else {
			$out['data'] = $response;
		}

		return $out;
	}

	public function smsTypes(): array
	{
		return ['error' => false, 'data' => []];
	}

	public function smsGetMessages(array $filter = []): array
	{
		//		$filterDto = new SmsInboxFilterDTO($filter);
//		$response = $this->smsRepository->getFilteredSmsInbox($filterDto);
//
//		if(isset($response['data']['response'])) {
//			$out['data'] = $response['data']['response'];
//		} else {
//			$out['error'] = 'Not found in response array data key [data][response]';
//		}

		return ['error' => false, 'data' => []];
	}

	public function callToPhone(int $project_id, string $phone_from, string $from_number, string $phone_to, string $from_name = '', array $options = []): array
	{
		$out = ['error' => false, 'data' => []];

		$data['project_id'] = $project_id;
		$data['voice']['from'] = $phone_from;
		$data['voice']['to'] = $phone_to;
		$data['voice']['from_number'] = $from_number;
		$data['voice']['from_name'] = $from_name;

		$data['voice']['options'] = $options; //['url'] = 'http://api-sales.dev.travelinsides.com/v1/twilio/request/?phone=+37369594567*/';

		$modelVoice = new ApiVoice();
		$modelVoice->project_id = $project_id;

		if($modelVoice->load($data) && !$modelVoice->validate()) {
			if ($errors = $modelVoice->getErrors()) {
				throw new \RuntimeException(VarDumper::dumpAsString($errors), 2);
			}
			throw new \RuntimeException('Not validate Voice data', 3);
		}

		$url = $this->host . '/v1/twilio/call-request?callerId=' . urlencode($modelVoice->from) . '&number='.urlencode($modelVoice->to) . '&from_number='.urlencode($modelVoice->from_number);
		$statusCall = $this->host . '/v1/twilio/voice-status-callback';

		$response = [];
		$call = null;

		try {

			$twilio = Yii::$app->twilio->client;

			$options = [
				'url' => $url,
				'statusCallback' => $statusCall,
				'statusCallbackMethod' => 'POST',
				'statusCallbackEvent' => ['initiated' , 'ringing', 'answered', 'completed'],
			];

			$call = $twilio->calls->create($modelVoice->from,
				$modelVoice->from_name ?: 'BotDialer',
				$options
			);

			$response = $options;
			$response['call']['sid'] = $call->sid;
			$response['call']['to'] = $call->to;
			$response['call']['from'] = $call->from;
			$response['call']['status'] = $call->status;
			$response['call']['price'] = $call->price;
			$response['call']['account_sid'] = $call->accountSid;
			$response['call']['api_version'] = $call->apiVersion;
			$response['call']['annotation'] = $call->annotation;
			$response['call']['uri'] = $call->uri;
			$response['call']['direction'] = $call->direction;
			$response['call']['phone_number_sid'] = $call->phoneNumberSid;
			$response['call']['caller_name'] = $call->callerName;
			$response['call']['start_time'] = $call->startTime;
			$response['call']['date_created'] = $call->dateCreated;
			$response['call']['date_updated'] = $call->dateUpdated;
		} catch (\Throwable $e) {

			Yii::error($e->getTraceAsString(), 'API:VoiceController:CallToPhone:calls:create');

//			$message = $e->getMessage().' (code:'.$e->getCode().', line: '.$e->getLine().')';
			$message = AppHelper::throwableFormatter($e);
			$response['error'] = $message;
			$response['error_code'] = 20;
		}

		if(isset($response['error']) && $response['error']) {
			$out['error'] = $response['error'];
		} else {
			$out['data'] = $response;
		}

		return $out;
	}

	public function updateCall(string $sid, array $updateData = []): array
	{
		$out = ['error' => false, 'data' => []];

		try {
			if(!$sid) {
				throw new \RuntimeException('Params "CALL SID" is empty', 5);
			}

			if(!$updateData) {
				throw new \RuntimeException('Params "Update Data" is empty', 6);
			}

			$data['sid'] = $sid;
			$data['data'] = $updateData;

			$twilio = \Yii::$app->twilio->client;
			$call = $twilio->calls($sid)->update($updateData);

			$response['sid'] = $sid; //$call->properties;
			$response['call']['status'] = $call->status;
			$response['call']['sid'] = $call->sid;
			$response['call']['from'] = $call->from;
			$response['call']['to'] = $call->to;
			$response['call']['duration'] = $call->duration;

		} catch (\Throwable $e) {

			Yii::error($e->getTraceAsString(), 'TwilioModule:TwilioCommunicationService:updateCall:Throwable');

			$response['error'] = $e->getMessage();
			$response['error_code'] = 20;
		}

		if(isset($response['error']) && $response['error']) {
			$out['error'] = $response['error'];
		} else {
			$out['data'] = $response;
		}

		return $out;
	}

	public function redirectCall(string $sid, array $data = [], string $callBackUrl = ''): array
	{
		$out = ['error' => false, 'data' => []];

		if (!$sid) {
			throw new \RuntimeException('Params "CALL SID" is empty', 5);
		}

		if (!$callBackUrl) {
			throw new \RuntimeException('Params "callBackUrl" is empty', 7);
		}

		try {
			$twilio = \Yii::$app->twilio->client;

			$params['sid'] = $sid;
			$params['data'] = $data;
			$params['callBackUrl'] = $callBackUrl;

			$paramsStr = json_encode($params);

			$updateData = [
				'method' => 'POST',
				'url' => $this->host . '/v1/twilio/redirect-call-middleware/?params=' . urlencode($paramsStr),
			];

			$call = $twilio->calls($sid)->update($updateData);

			$response['sid'] = $sid; //$call->properties;
			$response['call']['status'] = $call->status;
			$response['call']['sid'] = $call->sid;
			$response['call']['from'] = $call->from;
			$response['call']['to'] = $call->to;
			$response['call']['duration'] = $call->duration;

		} catch (\Throwable $e) {

			Yii::error($e->getTraceAsString(), 'TwilioModule:TwilioCommunicationService:updateCall:Throwable');

			$message = $e->getMessage();
			$response['error'] = $message;
		}

		if(isset($response['error']) && $response['error']) {
			$out['error'] = $response['error'];
		} else {
			$out['data'] = $response;
		}

		return $out;
	}

	public function getJwtToken($username = ''): array
	{
		$out = ['error' => false, 'data' => []];

		try {
			if (!$username) {
				throw new \RuntimeException('Not found agent');
			}

			$token = TwilioJwtToken::findOne(['jt_agent' => $username]);
			if (!$token) {
				$token = new TwilioJwtToken();
				$token->jt_agent = $username;
				$token->jt_app_sid = $this->twilio_configs['app_sid'];
			}

			if (NULL !== $token->jt_expire_dt) {
				$dateNow = new \DateTime('now');
				$dateExpire = new \DateTime($token->jt_expire_dt);
				if ($dateExpire >= $dateNow) {
					$response['data'] = [
						'identity' => $token->jt_agent,
						'client' => $username,
						'token' => $token->jt_token,
						'app_sid' => $token->jt_app_sid,
						'account_sid' => $this->twilio_configs['account_sid'],
					];
					return $response;
				}
			}

			$capability = new ClientToken($this->twilio_configs['account_sid'], $this->twilio_configs['auth_token']);
			$capability->allowClientOutgoing($this->twilio_configs['app_sid']);
			$capability->allowClientIncoming($username);
			$tokenString = (string)$capability->generateToken(3600 * 4);

			$token->jt_app_sid = $this->twilio_configs['app_sid'];
			$dtNow = new \DateTime('now');
			$token->jt_token = $tokenString;
			$token->jt_created_dt = $dtNow->format("Y-m-d H:i:s");
			$dtNow->modify('+4 hours');
			$token->jt_expire_dt = $dtNow->format("Y-m-d H:i:s");

			if (!$token->save()) {
				throw new \Exception('Can not save twilio token to database');
			}

			$response['data'] = [
				'identity' => $token->jt_agent,
				'client' => $token->jt_agent,
				'token' => $token->jt_token,
				'expire' => $token->jt_expire_dt,
				'app_sid' => $token->jt_app_sid,
				'account_sid' => $this->twilio_configs['account_sid'],
			];
		} catch (\RuntimeException $e) {
			$response = [
				'error' => $e->getMessage() . ' :: ' . $e->getFile() . ' :: ' . $e->getLine(),
			];

			\Yii::error(VarDumper::dumpAsString($response), 'info\TwilioJwtController:actionGetToken:Throwable');
		}

		if(isset($response['error']) && $response['error']) {
			$out['error'] = $response['error'];
		} else {
			$out['data'] = $response['data'];
		}

		return $out;
	}

	public function getJwtTokenCache($username = '', $deleteCache = false)
	{
		$cacheKey = 'jwt_token_'.$username;

		if($deleteCache) {
			\Yii::$app->cache->delete($cacheKey);
		}
		$out = \Yii::$app->cache->get($cacheKey);

		if ($out === false) {
			$out = $this->getJwtToken($username);

			if($out && isset($out['data']['token']) && $out['data']['token']) {
				$expired = isset($out['data']['expire']) ? strtotime($out['data']['expire']) - time() : 60 * 30;
				\Yii::$app->cache->set($cacheKey, $out, $expired);
			}
		}

		return $out;
	}

	public function callRedirect($cid, $type, $from, $to, $firstTransferToNumber = false): array
	{
		$out = ['error' => false, 'data' => []];

		try {
			if (empty($cid)) {
				throw new \RuntimeException('Error request params (cid)', 6);
			}
			if (empty($to)) {
				throw new \RuntimeException('Error request params (redirect_to)', 7);
			}
			if (empty($type)) {
				throw new \RuntimeException('Error request params (type)', 8);
			}

			$client = new TwClient($this->twilio_configs['account_sid'], $this->twilio_configs['auth_token']);
			$call = $client->calls($cid);
			if (!$call) {
				throw new \RuntimeException('Error Call. CID:' . $cid, 9);
			}

			$paramsData = [
				'method' => 'POST',
				'url' => $this->host . '/v1/twilio/redirect-to/?to=' . urlencode($to) . '&type=' . $type.'&from='. urlencode($from),
			];

			if ($type === 'number' && !$firstTransferToNumber) {

				$parent_cid = $call->fetch()->parentCallSid;
				$parentCall = $client->calls($parent_cid);
				if (!$parentCall) {
					throw new \RuntimeException('Error Parent Call. Parent CID:' . $parent_cid, 10);
				}

				$result = $parentCall->update($paramsData)->toArray();
			} else {
				$result = $call->update($paramsData)->toArray();
			}
			$response['data'] = [
				'is_error' => false,
				'result' => $result,
			];
		} catch (\Throwable $e) {
			$response = [
				'message' => 'Call redirect error: ' . $e->getMessage(),
				'data' => [
					'is_error' => true,
					'result' => []
				],
			];
			\Yii::error(VarDumper::dumpAsString($response), 'API:TwilioJwtController:actionRedirectCall:Throwable');
		}

		if ($response['data']['is_error']) {
			$out['error'] = $response['message'];
		} else {
			$out['data'] = $response['data'];
		}

		return $out;
	}
}