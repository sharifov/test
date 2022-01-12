<?php

namespace src\services\clientChatService;

use src\model\clientChat\entity\statusLogReason\ClientChatStatusLogReason;
use src\model\clientChatStatusLog\entity\ClientChatStatusLog;
use src\repositories\clientChatStatusLogRepository\ClientChatStatusLogRepository;

/**
 * Class ClientChatStatusLogService
 * @package src\services\clientChatService
 *
 * @property ClientChatStatusLogRepository $clientChatStatusLogRepository
 */
class ClientChatStatusLogService
{
    /**
     * @var ClientChatStatusLogRepository
     */
    private ClientChatStatusLogRepository $clientChatStatusLogRepository;

    public function __construct(ClientChatStatusLogRepository $clientChatStatusLogRepository)
    {
        $this->clientChatStatusLogRepository = $clientChatStatusLogRepository;
    }

    public function log(
        int $chatId,
        ?int $fromStatus,
        int $toStatus,
        ?int $ownerId,
        ?string $description,
        ?int $userId,
        ?int $prevChannel,
        int $actionType,
        ?int $reasonId,
        ?string $rid
    ): ?int {
        if ($previous = $this->clientChatStatusLogRepository->getPrevious($chatId)) {
            $previous->end();
            $this->clientChatStatusLogRepository->save($previous);
        }
        $log = ClientChatStatusLog::create(
            $chatId,
            $fromStatus,
            $toStatus,
            $ownerId,
            $userId,
            $prevChannel,
            $actionType,
            $rid
        );
        $this->clientChatStatusLogRepository->save($log);
        if ($reasonId) {
            $statusLogReason = ClientChatStatusLogReason::create($log->csl_id, $reasonId, $description);
            $statusLogReason->save();
        }
        return $log->csl_id;
    }
}
