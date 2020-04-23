<?php


namespace modules\twilio\components\jobs;


use modules\twilio\src\services\sms\SmsCommunicationService;
use yii\base\BaseObject;
use yii\queue\JobInterface;

/**
 * Class SmsFinishJob
 * @package modules\twilio\components\jobs
 *
 * @property array $request_data
 */
class SmsFinishJob extends BaseObject implements JobInterface
{
	public array $request_data;

	public function execute($queue)
	{
		if(isset($this->request_data['smsData']['sq_id'])) {
			$smsCommunicationService = \Yii::createObject(SmsCommunicationService::class);
			$info = $smsCommunicationService->getSmsTwInfo($this->request_data['smsData']['sq_id']);
			$this->request_data['sms'] = $info['sms'];
			$this->request_data['smsData'] = $info['smsData'];

			$result = $smsCommunicationService->smsFinish($this->request_data);

			if ($result['error']) {
				\Yii::error($result['error'], 'TwilioModule::SmsFinishJob::execute::smsFinish::error');
				return false;
			}

			return true;
		}

		\Yii::error('Not found sq_id in smsData', 'TwilioModule::SmsFinishJob::execute::error');
		return false;
	}
}