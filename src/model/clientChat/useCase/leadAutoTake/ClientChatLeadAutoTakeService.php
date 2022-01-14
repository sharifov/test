<?php

namespace src\model\clientChat\useCase\leadAutoTake;

use common\models\Lead;
use common\models\LeadFlow;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChatLead\entity\ClientChatLead;
use src\repositories\lead\LeadRepository;

/**
 * Class ClientChatLeadAutoTakeService
 *
 * @property LeadRepository $leadRepository
 */
class ClientChatLeadAutoTakeService
{
    private LeadRepository $leadRepository;

    public function __construct(LeadRepository $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }

    public function byAcceptFromWidget(int $chatId, int $userId): void
    {
        $chat = ClientChat::find()->byId($chatId)->one();

        if (!$chat) {
            throw new \DomainException('Not found chat. Id: ' . $chatId);
        }

        if (!$chat->cch_channel_id) {
            return;
        }

        $settings = new ClientChatLeadAutoTakeSettings($chat->cchChannel);

        if (!$settings->isOnAcceptChat()) {
            return;
        }

        $lead = $this->getLead($chatId, $settings->getAvailableStatuses());

        if (!$lead) {
            return;
        }

        $lead->processing($userId, null, LeadFlow::DESCRIPTION_CLIENT_CHAT_AUTO_ASSIGN);
        $this->leadRepository->save($lead);
    }

    private function getLead(int $chatId, array $statuses): ?Lead
    {
        return Lead::find()
            ->andWhere(['id' => ClientChatLead::find()->select(['ccl_lead_id'])->andWhere(['ccl_chat_id' => $chatId])])
            ->andWhere(['IS', 'employee_id', null])
            ->andWhere(['status' => $statuses])
            ->orderBy(['id' => SORT_DESC])
            ->one();
    }
}
