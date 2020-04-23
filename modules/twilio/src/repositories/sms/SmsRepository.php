<?php

namespace modules\twilio\src\repositories\sms;

use common\models\Sms;

class SmsRepository
{
	public function getFilteredSmsInbox(SmsInboxFilterDTO $filterData)
	{
		$query = Sms::find()->where(['s_type_id' => Sms::FILTER_TYPE_INBOX]);

		$query->orderBy(['s_id' => $filterData->order]);

		if ($filterData->phone_list) {
			$query->andWhere(['or', ['s_phone_from' => $filterData->phone_list], ['s_phone_to' => $filterData->phone_list]]);
		}

		if ($filterData->project_list) {
			$query->andWhere(['s_project_id' => $filterData->project_list]);
		}

		if ($filterData->last_id) {
			$query->andWhere(['>','s_id', $filterData->last_id]);
		}

		if($filterData->last_dt) {
			$query->andWhere(['>=','s_created_dt', $filterData->last_dt]);
		}

		if($filterData->phone_to) {
			$query->andWhere(['LOWER(s_phone_to)' => mb_strtolower($filterData->phone_to)]);
		}

		if($filterData->phone_from) {
			$query->andWhere(['LOWER(s_phone_from)' => mb_strtolower($filterData->phone_from)]);
		}

		$count = $query->count();

		if($filterData->limit > 0) {
			$query->limit($filterData->limit);
		}

		if($filterData->offset > 0) {
			$query->offset($filterData->offset);
		}

		try {
			$sms = $query->asArray()->all();
			$response['sms'] = $sms;
			$response['pagination']['count'] = $count;
			$response['pagination']['limit'] = $filterData->limit;
			$response['pagination']['offset'] = $filterData->offset;
		} catch (\Throwable $e) {
			$message = $e->getTraceAsString();
			\Yii::error($message, 'API:Sms:InboxList:SmsIncoming');
			$message = $e->getMessage().' (code:'.$e->getCode().', line: '.$e->getLine().')';

			$response['error'] = $message;
			$response['error_code'] = 10;
		}

		$responseData['data']['response'] = $response;
		return $responseData;
	}
}