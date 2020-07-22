<?php

namespace sales\model\ClientChatVisitorData\repository;

use sales\model\ClientChatVisitorData\entity\ClientChatVisitorData;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;
use yii\helpers\VarDumper;

class ClientChatVisitorDataRepository extends Repository
{
	public function findByCcvId(int $id): ClientChatVisitorData
	{
		if ($visitorData = ClientChatVisitorData::findOne(['cvd_ccv_id' => $id])) {
			return $visitorData;
		}
		throw new NotFoundException('Client Chat Visitor Data not found by id: ' . $id);
	}

	public function createByClientChatRequest(int $ccvId, array $data): void
	{
		$visitorData = ClientChatVisitorData::createByClientChatRequest($ccvId, $data);
		if (!$visitorData->validate()) {
			foreach ($visitorData->errors as $attribute => $error) {
				$visitorData->{$attribute} = null;
			}
			\Yii::error('Client Chat Visitor validation failed: ' . VarDumper::dumpAsString($visitorData->errors), 'ClientChatVisitorDataRepository::createByClientChatRequest::validation');
		}

		try {
			$this->save($visitorData);
		} catch (\RuntimeException $e) {
			\Yii::error('Client Chat Visitor save failed: ' . VarDumper::dumpAsString($visitorData->errors), 'ClientChatVisitorDataRepository::createByClientChatRequest::save');
		}
	}

	public function updateByClientChatRequest(ClientChatVisitorData $visitorData, array $data): void
	{
		$visitorData->updateByClientChatRequest($data);
		if (!$visitorData->validate()) {
			foreach ($visitorData->errors as $attribute => $error) {
				$visitorData->{$attribute} = null;
			}
			\Yii::error('Client Chat Visitor Data validation failed: ' . VarDumper::dumpAsString($visitorData->errors), 'ClientChatVisitorDataRepository::updateByClientChatRequest::validation');
		}

		try {
			$this->save($visitorData);
		} catch (\RuntimeException $e) {
			\Yii::error('Client Chat Visitor Data save failed: ' . VarDumper::dumpAsString($visitorData->errors), 'ClientChatVisitorDataRepository::updateByClientChatRequest::save');
		}
	}

	public function save(ClientChatVisitorData $clientChatVisitorData): int
	{
		if (!$clientChatVisitorData->save(false)) {
			throw new \RuntimeException('Client Chat Visitor Data saving failed');
		}
		return $clientChatVisitorData->cvd_id;
	}
}