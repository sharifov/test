<?php

namespace sales\model\clientChat\componentRule\component;

use frontend\helpers\JsonHelper;
use sales\model\clientChat\componentEvent\component\ComponentDTOInterface;

class SaveFlightQuoteSearchData implements RunnableComponentInterface
{
    public function run(ComponentDTOInterface $dto): void
    {
        if (($clientChatRequest = $dto->getClientChatRequest()) && $parameters = $clientChatRequest->getFlightSearchParameters()) {
        }
    }

    public function getDefaultConfig(): array
    {
        return [];
    }

    public function getDefaultConfigJson(): string
    {
        return JsonHelper::encode($this->getDefaultConfig());
    }
}
