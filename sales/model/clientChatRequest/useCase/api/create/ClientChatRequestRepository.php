<?php

namespace sales\model\clientChatRequest\useCase\api\create;

use sales\model\clientChatRequest\entity\ClientChatRequest;

class ClientChatRequestRepository
{
	/**
	 * @param ClientChatRequest $model
	 * @return ClientChatRequest
	 */
	public function save(ClientChatRequest $model): ClientChatRequest
	{
		if (!$model->save(false)) {
			throw new \RuntimeException($model->getErrorSummary(false)[0]);
		}
		return $model;
	}
}