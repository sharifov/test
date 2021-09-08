<?php

namespace sales\model\clientChat\componentRule\component;

use frontend\helpers\JsonHelper;
use sales\model\clientChat\componentEvent\component\ComponentDTOInterface;
use sales\model\clientChat\componentRule\component\defaultConfig\CreateLeadOnRoomConnectedConfig;
use sales\model\lead\useCases\lead\create\LeadCreateByChatForm;
use sales\model\lead\useCases\lead\create\LeadManageService;

/**
 * Class CreateLeadOnRoomConnected
 * @package sales\model\clientChat\componentRule\component
 *
 * @property-read LeadManageService $leadManageService
 */
class CreateLeadOnRoomConnected implements RunnableComponentInterface
{
    private LeadManageService $leadManageService;

    public function __construct(LeadManageService $leadManageService)
    {
        $this->leadManageService = $leadManageService;
    }

    public function run(ComponentDTOInterface $dto): void
    {
        $createLeadIfAlreadyExists = $this->getDefaultConfig()['create_lead_if_already_exists'] ?? false;
        $chat = $dto->getClientChatEntity();

        if ($chat && ((($leads = $chat->leads) && $createLeadIfAlreadyExists) || (!$leads))) {
            $createLeadByChatForm = new LeadCreateByChatForm($chat);

            if ($createLeadByChatForm->validate()) {
                $this->leadManageService->createByClientChat($createLeadByChatForm, $chat, null);
            }
        }
    }

    public function getDefaultConfig(): array
    {
        return CreateLeadOnRoomConnectedConfig::getConfig();
    }

    public function getDefaultConfigJson(): string
    {
        return JsonHelper::encode($this->getDefaultConfig());
    }
}
