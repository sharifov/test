<?php

namespace sales\model\clientChat\services;

use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\repositories\cases\CasesRepository;

/**
 * Class ClientChatAssignService
 *
 * @property ClientChatRepository $chatRepository
 * @property CasesRepository $casesRepository
 */
class ClientChatAssignService
{
    private ClientChatRepository $chatRepository;
    private CasesRepository $casesRepository;

    public function __construct(ClientChatRepository $chatRepository, CasesRepository $casesRepository)
    {
        $this->chatRepository = $chatRepository;
        $this->casesRepository = $casesRepository;
    }

    public function assignCase(int $chatId, int $caseId): void
    {
        $chat = $this->chatRepository->findById($chatId);
        $chat->assignCase($caseId);
        $this->chatRepository->save($chat);
    }

    public function assignLead(int $chatId, int $leadId): void
    {
        $chat = $this->chatRepository->findById($chatId);
        $chat->assignLead($leadId);
        $this->chatRepository->save($chat);
    }
}
