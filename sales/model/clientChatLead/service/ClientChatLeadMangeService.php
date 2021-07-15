<?php

namespace sales\model\clientChatLead\service;

use common\models\Lead;
use sales\helpers\ErrorsToStringHelper;
use sales\model\clientChatLead\entity\ClientChatLead;
use sales\model\clientChatLead\entity\ClientChatLeadRepository;

/**
 * Class ClientChatLeadMangeService
 * @package sales\model\clientChatLead\service
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
