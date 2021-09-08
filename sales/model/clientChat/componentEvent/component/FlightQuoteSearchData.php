<?php

namespace sales\model\clientChat\componentEvent\component;

use sales\model\clientChat\componentEvent\component\defaultConfig\DefaultConfig;

/**
 * Class FlightQuoteSearchData
 * @package sales\model\clientChat\componentEvent\component
 */
class FlightQuoteSearchData implements ComponentEventInterface
{
    public function run(ComponentDTOInterface $dto): string
    {
        if (($clientChatRequest = $dto->getClientChatRequest()) && $clientChatRequest->getFlightSearchParameters()) {
            return 'true';
        }
        return 'false';
    }

    public function getDefaultConfig(): array
    {
        return DefaultConfig::getConfig();
    }

    public function getDefaultConfigJson(): string
    {
        return DefaultConfig::getConfigJson();
    }
}
