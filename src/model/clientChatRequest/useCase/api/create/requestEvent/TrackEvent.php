<?php

namespace src\model\clientChatRequest\useCase\api\create\requestEvent;

use src\model\clientChatRequest\entity\ClientChatRequest;
use src\model\clientChatVisitorData\repository\ClientChatVisitorDataRepository;

/**
 * Class TrackEvent
 * @package src\model\clientChatRequest\useCase\api\create\requestEvent
 *
 * @property ClientChatVisitorDataRepository $clientChatVisitorDataRepository
 */
class TrackEvent implements ChatRequestEvent
{
    /**
     * @var ClientChatVisitorDataRepository
     */
    private ClientChatVisitorDataRepository $clientChatVisitorDataRepository;

    public function __construct(ClientChatVisitorDataRepository $clientChatVisitorDataRepository)
    {
        $this->clientChatVisitorDataRepository = $clientChatVisitorDataRepository;
    }

    public function process(ClientChatRequest $request): void
    {
        $cchVisitorData = $this->clientChatVisitorDataRepository->findByVisitorRcId($request->getClientRcId());
        $this->clientChatVisitorDataRepository->updateByClientChatRequest($cchVisitorData, $request->getDecodedData());
    }

    public function getClassName(): string
    {
        return self::class;
    }
}
