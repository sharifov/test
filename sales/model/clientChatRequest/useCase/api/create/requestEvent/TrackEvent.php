<?php

namespace sales\model\clientChatRequest\useCase\api\create\requestEvent;

use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatVisitorData\repository\ClientChatVisitorDataRepository;

/**
 * Class TrackEvent
 * @package sales\model\clientChatRequest\useCase\api\create\requestEvent
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
