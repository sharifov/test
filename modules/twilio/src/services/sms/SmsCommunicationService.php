<?php
namespace modules\twilio\src\services\sms;

use common\models\Sms;
use modules\twilio\components\jobs\SendSmsJob;
use modules\twilio\components\jobs\SmsFinishJob;
use modules\twilio\src\entities\SmsForm;
use modules\twilio\src\repositories\sms\SmsRepository;
use sales\helpers\app\AppHelper;
use sales\model\sms\entity\smsDistributionList\SmsDistributionList;
use sales\services\sms\incoming\SmsIncomingForm;
use sales\services\sms\incoming\SmsIncomingService;
use Yii;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\queue\Queue;

/**
 * Class SmsCommunicationService
 * @package modules\twilio\src\services\sms
 *
 * @property SmsRepository $smsRepository
 */
class SmsCommunicationService
{
	/**
	 * @var SmsRepository
	 */
	private SmsRepository $smsRepository;

	public function __construct(SmsRepository $smsRepository)
	{
		$this->smsRepository = $smsRepository;
	}

	public function sendSms(SmsForm $smsForm, int $delay = 0): array
	{
		$job = new SendSmsJob();
		$job->phone_to = $smsForm->sq_phone_to;
		$job->phone_from = $smsForm->sq_phone_from;
		$job->sms_text = $smsForm->sq_sms_text;
		$job->s_id = $smsForm->s_id;

		/** @var Queue $queue */
		$queue = Yii::$app->queue_job;

		if ($delay > 0) {
			$queue->delay($delay);
		}

		$job_id = $queue->push($job);

		$response = [];
		if ($job_id) {
			$response['sq_job_id'] = $job_id;
			$response['sq_updated_dt'] = date('Y-m-d H:i:s');

			if ($delay > 0) {
				$response['sq_status_id'] = Sms::STATUS_PENDING;
			} else {
				$response['sq_status_id'] = Sms::STATUS_PROCESS;
			}

			return $response;
		}

		return $response;
	}

	public function addSmsFinishJob(Sms $sms, int $delay = 0): void
	{
		$data = [
			'sq_id' => $sms->s_id,
			'sq_status_id' => $sms->s_status_id,
			'sq_project_id' => $sms->s_project_id,
			'sq_price' => $sms->s_tw_price,
			'sq_num_segments' => $sms->s_tw_num_segments,
			'sms' => $sms->attributes,
			'smsData' => ['sq_id' => $sms->s_id],
		];

		$job = new SmsFinishJob();
		$job->request_data = $data;
		$queue = \Yii::$app->queue_job;
		$queue->delay($delay);
		$queue->push($job);
	}

	public function smsFinish(array $post = []): array
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

			$smsData = $post['smsData'];
			$comId = $post['sq_id'];

			if(!$smsData) {
				throw new \RuntimeException('Not found smsData', 11);
			}

			if(!$smsData['sid']) {
				throw new \RuntimeException('Not found smsData[sid]', 12);
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

				$smsModel = SmsDistributionList::find()->where(['sdl_com_id' => $comId])->one();

				if ($smsModel) {
					if(isset($smsData['price'])) {
						$smsModel->sdl_price = abs((float) $smsData['price']);
					}

					if(!empty($smsData['num_segments'])) {
						$smsModel->sdl_num_segments = (int) $smsData['num_segments'];
					}

					if(!empty($smsData['sid']) && !$smsModel->sdl_message_sid) {
						$smsModel->sdl_message_sid = $smsData['sid'];
					}

					if(isset($smsData['status'])) {

						$smsModel->sdl_error_message = 'status: ' . $smsData['status'];

						if($smsData['status'] === 'delivered') {
							$smsModel->sdl_status_id = SmsDistributionList::STATUS_DONE;
						}
					}

					if(!$smsModel->save()) {
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
			$message = AppHelper::throwableFormatter($throwable);
			$response['error'] = $message;
			$response['error_code'] = $throwable->getCode();
		}

		return $response;
	}

	/**
	 * @param Sms $sms
	 * @return mixed
	 */
	public function updateSmsStatus(Sms $sms)
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


		$sq_id = (int)$sms->s_id;
		$sq_status_id = (int) $sms->s_status_id;

		$smsParams = $sms->attributes;

		try {
			if(!$sq_id) {
				throw new \RuntimeException('Not found sq_id', 11);
			}

			if(!$sq_status_id) {
				throw new \RuntimeException('Not found sq_status_id', 12);
			}

			$sid =  $smsParams['sq_tw_message_id'] ?? null;

//			$sms = null;

//            if($sid) {
//                $sms = Sms::findOne(['s_tw_message_sid' => $sid]);
//            }

//			if(!$sms) {
//				$sms = Sms::findOne(['s_' => $sq_id]);
//			}


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
				$response['SmsId'] = $sms->s_id;
			} else {

				$smsModel = SmsDistributionList::find()->where(['sdl_id' => $sq_id])->one();

				if ($smsModel) {

					if ($sq_status_id > 0) {
						$smsModel->sdl_status_id = $sq_status_id;

						if ($smsParams) {
							if (!empty($smsParams['sq_tw_price'])) {
								$smsModel->sdl_price = abs((float)$smsParams['sq_tw_price']);
							}

							if (!empty($smsParams['sq_tw_num_segments'])) {
								$smsModel->sdl_num_segments = (int)$smsParams['sq_tw_num_segments'];
							}

							if (!empty($smsParams['sq_tw_status'])) {
								$smsModel->sdl_error_message = 'status: ' . $smsParams['sq_tw_status'];
							}

							if (!$smsModel->sdl_message_sid && !empty($smsParams['sq_tw_message_id'])) {
								$smsModel->sdl_message_sid = $smsParams['sq_tw_message_id'];
							}

						}

						if (!$smsModel->save()) {
							Yii::error(VarDumper::dumpAsString($sms->errors),
								'API:Communication:updateSmsStatus:SmsDistributionList:save');
						}
					}

					$response['SmsDistributionId'] = $smsModel->sdl_id;

				} else {
					$response['error'] = 'Not found SMS or SmsDistributionList ID (' . $sq_id . ')';
					$response['error_code'] = 13;
				}
			}


		} catch (\Throwable $throwable) {
			Yii::error($throwable->getTraceAsString(), 'API:Communication:updateSmsStatus:try');
			$message = AppHelper::throwableFormatter($throwable);
			$response['error'] = $message;
			$response['error_code'] = 15;
		}

		return $response;
	}

	/**
	 * @param array $post
	 * @return array
	 */
	public function newSmsMessagesReceived(array $post = []): array
	{
		$response = [];

		$smsItem = $post;

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
		} catch (\Throwable $e) {
			Yii::error($e->getTraceAsString(), 'API:Communication:newSmsMessagesReceived:Sms:try');
			$message = $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
			$response['error'] = $message;
			if(!isset($response['error_code']) || !$response['error_code']) {
				$response['error_code'] = 15;
			}
		}

		return $response;
	}

	/**
	 * @param int $smsId
	 * @return array
	 */
	public function getSmsTwInfo(int $smsId): array
	{
		$sms = Sms::findOne($smsId);

		if(!$sms) {
			return [
				'sms' => [],
				'smsData' => [],
			];
		}

		$twInfo = $this->getTwilioInfo($sms);
		if($twInfo && is_array($twInfo) && isset($twInfo['sid'])) {
			$sms->s_tw_price = $twInfo['price'];
			$sms->save();
		}
		return [
			'sms' => $sms->attributes,
			'smsData' => $twInfo,
		];
	}

	/**
	 * @param Sms $sms
	 * @return array|mixed
	 */
	private function getTwilioInfo(Sms $sms)
	{
		try {

			$responseData = [];
			$response = null;
			$request = null;
			$url = '';

			$client = new Client();
			$client->setTransport(CurlTransport::class);
			$request = $client->createRequest();

			$authStr = base64_encode(\Yii::$app->twilio->account_sid . ':' . \Yii::$app->twilio->auth_token);
			$request->addHeaders(['Authorization' => 'Basic ' . $authStr]);
			$url = 'https://api.twilio.com/2010-04-01/Accounts/AC10f3c74efba7b492cbd7dca86077736c/Messages/' . $sms->s_tw_message_sid . '.json';

			$request->setMethod('GET')
				->setUrl($url)->setOptions([
					CURLOPT_CONNECTTIMEOUT => 10,
					CURLOPT_TIMEOUT => 10,
				]);
			$response = $request->send();

			if ($response->isOk) {
				$responseData = json_decode($response->content, true);
				return $responseData;
			}

			throw new \Exception('Error get Info for SMS from TW API. SMS SID:' . $sms->s_tw_message_sid);
		} catch (\Throwable $e) {
			$responseData = [];
			\Yii::error(VarDumper::dumpAsString([$e->getMessage(), $url, $request, $response]), 'API:Model:SmsQueue:getTwilioInfo:Throwable');
		}
		return $responseData;
	}
}