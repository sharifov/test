<?php

namespace sales\model\visitorSubscription\service;

use sales\model\clientChatForm\form\ClientChatSubscribeForm;
use sales\model\visitorSubscription\entity\VisitorSubscription;
use sales\model\visitorSubscription\repository\VisitorSubscriptionRepository;

class VisitorSubscriptionApiManageService
{
    /**
     * @var VisitorSubscriptionRepository
     */
    private VisitorSubscriptionRepository $repository;

    public function __construct(VisitorSubscriptionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createFlizzardSubscription(ClientChatSubscribeForm $form): void
    {
        $subscription = VisitorSubscription::createByApi($form->subscription_uid, $form->expired_date);
        $subscription->enabled();
        $subscription->setFlizzardType();

        $this->repository->save($subscription);
    }
}
