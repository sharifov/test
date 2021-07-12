<?php

namespace sales\model\visitorSubscription\repository;

use sales\dispatchers\EventDispatcher;
use sales\model\visitorSubscription\entity\VisitorSubscription;
use sales\repositories\NotFoundException;

/**
 * Class VisitorSubscriptionRepository
 * @package sales\model\visitorSubscription\repository
 *
 * @property-read EventDispatcher $eventDispatcher
 */
class VisitorSubscriptionRepository
{
    private EventDispatcher $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function save(VisitorSubscription $subscription): void
    {
        if (!$subscription->save()) {
            throw new \RuntimeException('Visitor subscription saving error: ' . $subscription->getErrorSummary(true)[0]);
        }
        $this->eventDispatcher->dispatchAll($subscription->releaseEvents());
    }

    public function findByUid(string $uid): VisitorSubscription
    {
        if ($subscription = VisitorSubscription::findOne(['vs_subscription_uid' => $uid])) {
            return $subscription;
        }
        throw new NotFoundException('Subscription not found by uid: ' . $uid);
    }
}
