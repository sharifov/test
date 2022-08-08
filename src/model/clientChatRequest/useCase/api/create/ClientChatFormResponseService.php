<?php

namespace src\model\clientChatRequest\useCase\api\create;

use src\model\clientChat\entity\ClientChatQuery;
use src\model\clientChatFeedback\ClientChatFeedbackRepository;
use src\model\clientChatForm\entity\ClientChatFormQuery;
use src\model\clientChatFormResponse\ClientChatFormResponseRepository;
use src\model\clientChatFormResponse\entity\ClientChatFormResponse;
use src\model\clientChatFormResponse\entity\ClientChatFormResponseQuery;

/**
 * Class ClientChatRequestService
 * @package src\model\clientChatRequest\useCase\api\create
 *
 * @property ClientChatFeedbackRepository $clientChatFeedbackRepository
 */
class ClientChatFormResponseService
{
    private ClientChatFormResponseRepository $clientChatFormResponseRepository;

    /**
     * ClientChatRequestService constructor.
     * @param ClientChatFormResponseRepository $clientChatFormResponseRepository
     */
    public function __construct(
        ClientChatFormResponseRepository $clientChatFormResponseRepository
    ) {
        $this->clientChatFormResponseRepository = $clientChatFormResponseRepository;
    }

    public function createFormResponse(string $rid, string $formKey, string $formValue, string $createdAt): ?ClientChatFormResponse
    {
        // Getting client chat by room id
        $clientChat = ClientChatQuery::lastSameChat($rid);

        if (is_null($clientChat)) {
            throw new \RuntimeException("client chat with room id `{$rid}` not found");
        }

        $clientChatForm = ClientChatFormQuery::getByKey($formKey);

        if (is_null($clientChatForm)) {
            throw new \RuntimeException("client chat form with room id `{$rid}` not found");
        }

        if (
            ClientChatFormResponseQuery::checkDuplicateValue(
                $clientChatForm->ccf_id,
                $clientChat->cch_id,
                $formValue
            )
        ) {
            return null;
        }

        $model = ClientChatFormResponse::create(
            $rid,
            $clientChat->cch_id,
            $clientChatForm->ccf_id,
            $formValue,
            $createdAt
        );

        $this->clientChatFormResponseRepository->save($model);

        return $model;
    }
}
