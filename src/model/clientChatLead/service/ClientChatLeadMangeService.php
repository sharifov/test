<?php

namespace src\model\clientChatLead\service;

use common\models\Lead;
use src\helpers\ErrorsToStringHelper;
use src\model\clientChatLead\entity\ClientChatLead;
use src\model\clientChatLead\entity\ClientChatLeadRepository;

/**
 * Class ClientChatLeadMangeService
 * @package src\model\clientChatLead\service
 *
 * @property-read ClientChatLeadRepository $repository
 */
class ClientChatLeadMangeService
{
    /**
     * @var ClientChatLeadRepository
     */
    private ClientChatLeadRepository $repository;

    public function __construct(ClientChatLeadRepository $repository)
    {
        $this->repository = $repository;
    }

    public function assignChatByLeadIds(array $leadIds, int $chatId): array
    {
        $errors = [];

        foreach ($leadIds as $leadId) {
            $clientChatLead = ClientChatLead::create($chatId, $leadId, new \DateTimeImmutable('now'));
            if ($clientChatLead->validate()) {
                $this->repository->save($clientChatLead);
            } else {
                $errors[] = ErrorsToStringHelper::extractFromModel($clientChatLead);
            }
        }
        return $errors;
    }
}
