<?php

namespace sales\model\clientChatRequest\useCase\api\create;

use sales\model\clientChatRequest\entity\ClientChatRequest;

class ClientChatRequestRepository
{
	/**
	 * @param ClientChatRequestApiForm $form
	 * @return ClientChatRequest
	 * @throws \JsonException
	 */
	public function create(ClientChatRequestApiForm $form): ClientChatRequest
	{
		$clientChatRequest = ClientChatRequest::createByApi($form);
		return $this->save($clientChatRequest);
	}

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