<?php

namespace src\model\clientChatVisitorData\service;

use src\model\clientChatVisitor\repository\ClientChatVisitorRepository;
use src\model\clientChatVisitorData\repository\ClientChatVisitorDataRepository;
use src\repositories\NotFoundException;

/**
 * Class ChatVisitorDataService
 * @package src\model\clientChatVisitorData\service
 *
 * @property ClientChatVisitorDataRepository $clientChatVisitorDataRepository
 * @property ClientChatVisitorRepository $clientChatVisitorRepository
 */
class ChatVisitorDataService
{
    /**
     * @var ClientChatVisitorDataRepository
     */
    private ClientChatVisitorDataRepository $clientChatVisitorDataRepository;
    /**
     * @var ClientChatVisitorRepository
     */
    private ClientChatVisitorRepository $clientChatVisitorRepository;

    public function __construct(
        ClientChatVisitorDataRepository $clientChatVisitorDataRepository,
        ClientChatVisitorRepository $clientChatVisitorRepository
    ) {
        $this->clientChatVisitorDataRepository = $clientChatVisitorDataRepository;
        $this->clientChatVisitorRepository = $clientChatVisitorRepository;
    }

    /**
     * @param int $chatId
     * @param int $clientId
     * @param string $visitorRcId
     * @param array $data
     */
    public function manageChatVisitorData(int $chatId, int $clientId, string $visitorRcId, array $data): void
    {
        try {
            $visitorData = $this->clientChatVisitorDataRepository->findByVisitorRcId($visitorRcId);
            $this->clientChatVisitorDataRepository->updateByClientChatRequest($visitorData, $data);
            $this->clientChatVisitorDataRepository->save($visitorData);
            if (!$this->clientChatVisitorRepository->exists($chatId, $visitorData->cvd_id)) {
                $this->clientChatVisitorRepository->create($chatId, $visitorData->cvd_id, $clientId);
            }
        } catch (NotFoundException $e) {
            $visitorData = $this->clientChatVisitorDataRepository->createByClientChatRequest($visitorRcId, $data);
            $this->clientChatVisitorRepository->create($chatId, $visitorData->cvd_id, $clientId);
        }
    }
}
