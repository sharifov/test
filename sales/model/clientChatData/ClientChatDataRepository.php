<?php
namespace sales\model\clientChatData;

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatData\entity\ClientChatData;
use sales\repositories\Repository;
use yii\helpers\VarDumper;

class ClientChatDataRepository extends Repository
{
	/**
	 * @param int $cchId
	 * @return bool
	 */
	public function exist(int $cchId): bool
	{
		return ClientChatData::find()->byCchId($cchId)->exists();
	}

	public function createByClientChatRequest(ClientChat $clientChat, array $data): void
	{
		$clientChatData = ClientChatData::createByClientChatRequest($clientChat->cch_id, $data);
		if (!$clientChatData->validate()) {
			foreach ($clientChatData->errors as $attribute => $error) {
				$clientChatData->{$attribute} = null;
			}
			\Yii::error('ClientChatData validation failed: ' . VarDumper::dumpAsString($clientChatData->errors), 'ClientChatDataRepository::createByClientChatRequest::validation');
		}

		try {
			$this->save($clientChatData);
		} catch (\RuntimeException $e) {
			\Yii::error('ClientChatData save failed: ' . VarDumper::dumpAsString($clientChatData->errors), 'ClientChatRequestService::createByClientChatRequest::save');
		}
	}

	public function clone(ClientChat $clientChat, ClientChatData $clientChatData): ClientChatData
	{
		$_data = new ClientChatData();
		$_data->ccd_cch_id = $clientChat->cch_id;
		$_data->ccd_country = $clientChatData->ccd_country;
		$_data->ccd_region = $clientChatData->ccd_region;
		$_data->ccd_city = $clientChatData->ccd_city;
		$_data->ccd_latitude = $clientChatData->ccd_latitude;
		$_data->ccd_longitude = $clientChatData->ccd_longitude;
		$_data->ccd_url = $clientChatData->ccd_url;
		$_data->ccd_title = $clientChatData->ccd_title;
		$_data->ccd_referrer = $clientChatData->ccd_referrer;
		$_data->ccd_timezone = $clientChatData->ccd_timezone;
		$_data->ccd_local_time = $clientChatData->ccd_local_time;

		return $_data;
	}

	public function save(ClientChatData $clientChatData): ClientChatData
	{
		if (!$clientChatData->save(false)) {
			throw new \RuntimeException('Client Chat Data saving failed');
		}
		return $clientChatData;
	}

	public function findByCchId(int $id): ?ClientChatData
	{
		return ClientChatData::findOne(['ccd_cch_id' => $id]);
	}
}