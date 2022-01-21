<?php

namespace src\model\clientChat\componentEvent\service;

use src\model\clientChat\componentEvent\entity\ClientChatComponentEvent;
use src\model\clientChat\componentEvent\form\ComponentEventCreateForm;
use src\model\clientChat\componentEvent\repository\ClientChatComponentEventRepository;
use src\model\clientChat\componentRule\entity\ClientChatComponentRuleQuery;
use src\model\clientChat\componentRule\repository\ClientChatComponentRuleRepository;
use src\services\TransactionManager;

/**
 * Class ClientChatComponentEventManageService
 * @package src\model\clientChat\componentEvent\service
 *
 * @property-read TransactionManager $transactionManager
 * @property-read ClientChatComponentEventRepository $clientChatComponentEventRepository
 * @property-read ClientChatComponentRuleRepository $clientChatComponentRuleRepository
 */
class ClientChatComponentEventManageService
{
    /**
     * @var TransactionManager
     */
    private TransactionManager $transactionManager;
    /**
     * @var ClientChatComponentEventRepository
     */
    private ClientChatComponentEventRepository $clientChatComponentEventRepository;
    /**
     * @var ClientChatComponentRuleRepository
     */
    private ClientChatComponentRuleRepository $clientChatComponentRuleRepository;

    public function __construct(
        TransactionManager $transactionManager,
        ClientChatComponentEventRepository $clientChatComponentEventRepository,
        ClientChatComponentRuleRepository $clientChatComponentRuleRepository
    ) {
        $this->transactionManager = $transactionManager;
        $this->clientChatComponentEventRepository = $clientChatComponentEventRepository;
        $this->clientChatComponentRuleRepository = $clientChatComponentRuleRepository;
    }

    public function createWithRules(ComponentEventCreateForm $form): int
    {
        return $this->transactionManager->wrap(function () use ($form) {
            $componentEventId = $this->clientChatComponentEventRepository->save($form->componentEvent);

            foreach ($form->componentRules as $componentRule) {
                $componentRule->cccr_component_event_id = $componentEventId;
                $this->clientChatComponentRuleRepository->save($componentRule);
            }

            return $componentEventId;
        });
    }

    public function updateWithRules(ClientChatComponentEvent $model, ComponentEventCreateForm $form): void
    {
        $this->transactionManager->wrap(function () use ($model, $form) {
            $model->setAttributes($form->componentEvent->toArray());
            $this->clientChatComponentEventRepository->save($model);

            ClientChatComponentRuleQuery::deleteByComponentEventId($model->ccce_id);

            foreach ($form->componentRules as $componentRule) {
                $componentRule->cccr_component_event_id = $model->ccce_id;
                $this->clientChatComponentRuleRepository->save($componentRule);
            }
        });
    }
}
