<?php

namespace src\model\clientChatCase\service;

use src\entities\cases\Cases;
use src\helpers\ErrorsToStringHelper;
use src\model\clientChatCase\entity\ClientChatCase;
use src\model\clientChatCase\entity\ClientChatCaseRepository;

/**
 * Class ClientChatCaseManageService
 * @package src\model\clientChatCase\service
 *
 * @property-read ClientChatCaseRepository $repository
 */
class ClientChatCaseManageService
{
    /**
     * @var ClientChatCaseRepository
     */
    private ClientChatCaseRepository $repository;

    public function __construct(ClientChatCaseRepository $repository)
    {
        $this->repository = $repository;
    }

    public function assignChatByCaseIds(array $ids, int $chatId): array
    {
        $errors = [];

        foreach ($ids as $caseId) {
            $clientChatCase = ClientChatCase::create($chatId, $caseId, new \DateTimeImmutable('now'));
            if ($clientChatCase->validate()) {
                $this->repository->save($clientChatCase);
            } else {
                $errors[] = ErrorsToStringHelper::extractFromModel($clientChatCase);
            }
        }
        return $errors;
    }
}
