<?php

namespace sales\model\clientChat\componentRule\component;

use frontend\helpers\JsonHelper;
use sales\model\clientChat\componentEvent\component\ComponentDTOInterface;
use sales\model\clientChatDataRequest\entity\ClientChatDataRequest;
use sales\model\clientChatDataRequest\entity\ClientChatDataRequestRepository;

/**
 * Class SaveFlightQuoteSearchData
 * @package sales\model\clientChat\componentRule\component
 *
 * @property-read ClientChatDataRequestRepository $repository
 */
class SaveFlightQuoteSearchData implements RunnableComponentInterface
{
    private ClientChatDataRequestRepository $repository;

    public function __construct(ClientChatDataRequestRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run(ComponentDTOInterface $dto): void
    {
        if (($clientChatRequest = $dto->getClientChatRequest()) && $parameters = $clientChatRequest->getFlightSearchParameters()) {
            $dataRequest = ClientChatDataRequest::create($dto->getClientChatEntity()->cch_id, $parameters);
            $this->repository->save($dataRequest);
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
