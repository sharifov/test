<?php
namespace modules\twilio\components\jobs;

use common\models\Sms;
use sales\model\sms\entity\smsDistributionList\SmsDistributionList;
use yii\base\BaseObject;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;

/**
 * Class SendSmsJob
 * @package modules\twilio\components\jobs
 *
 * @property int $s_id
 * @property string $phone_from
 * @property string $phone_to
 * @property string $sms_text
 */
class SendSmsJob extends BaseObject implements JobInterface
{
	public $phone_from;
	public $phone_to;
	public $sms_text;
	public $s_id;

	public function execute($queue)
	{
		$twilio = \Yii::$app->twilio->client;

		$messageOptions = [
			'from'              => $this->phone_from,
			'body'              => $this->sms_text,
			'statusCallback'    => \Yii::$app->twilio->messagingStatusCallbackUri, // \Yii::$app->params['twilio']['messagingStatusCallbackUri'],
			//'provideFeedback'   => true
		];

		$message = $twilio->messages->create($this->phone_to, $messageOptions);

		if($message->sid) {
			if($this->s_id) {
				$sms = Sms::findOne(['s_id' => $this->s_id]);
				if ($sms) {
					$sms->s_status_done_dt = date('Y-m-d H:i:s');
					$sms->s_updated_dt = date('Y-m-d H:i:s');
					$sms->s_status_id = Sms::STATUS_DONE;
					$sms->s_tw_message_sid = $message->sid;
					$sms->s_tw_num_segments = $message->numSegments;
					$sms->s_tw_price = $message->price;
					$sms->s_error_message = $message->status ? 'status: ' . $message->status : null;
					if (!$sms->save()) {
						\Yii::error(VarDumper::dumpAsString($sms->errors, 10), 'SendSmsJob:SmsQueue:save');
					}
					return true;
				}

				$smsDistribution = SmsDistributionList::findOne(['sdl_id' => $this->s_id]);
				if ($smsDistribution) {
					$smsDistribution->sdl_status_id = SmsDistributionList::STATUS_DONE;
					$smsDistribution->sdl_price = $message->price;
					$smsDistribution->sdl_num_segments = $message->numSegments;
					$smsDistribution->sdl_error_message = $message->status ? 'status: ' . $message->status : null;
					$smsDistribution->sdl_message_sid = $message->sid;
					if (!$sms->save()) {
						\Yii::error(VarDumper::dumpAsString($sms->errors, 10), 'SendSmsJob:SmsQueue:save');
					}
					return true;
				}
			}
			return true;
		}
		return false;
	}
}