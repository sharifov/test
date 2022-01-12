<?php

namespace src\model\visitorSubscription\service;

use src\model\clientChatForm\form\ClientChatSubscribeForm;
use src\model\visitorSubscription\entity\VisitorSubscription;
use src\model\visitorSubscription\repository\VisitorSubscriptionRepository;
use src\repositories\NotFoundException;

/**
 * Class VisitorSubscriptionApiManageService
 * @package src\model\visitorSubscription\service
 *
 * @property VisitorSubscriptionRepository $repository
 */
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
        try {
            $subscription = $this->repository->findByUid($form->subscription_uid);
            $subscription->enabled();
            if ($form->expired_date) {
                $subscription->vs_expired_date = $form->expired_date;
            }
        } catch (NotFoundException $e) {
            $subscription = VisitorSubscription::createByApi($form->subscription_uid, $form->expired_date);
            $subscription->enabled();
            $subscription->setFlizzardType();
        }

        $this->repository->save($subscription);
    }
}
