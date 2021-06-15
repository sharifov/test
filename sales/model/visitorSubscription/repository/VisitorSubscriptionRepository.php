<?php

namespace sales\model\visitorSubscription\repository;

use sales\model\visitorSubscription\entity\VisitorSubscription;
use sales\repositories\NotFoundException;

class VisitorSubscriptionRepository
{
    public function save(VisitorSubscription $subscription): int
    {
        if ($subscription->save()) {
            return $subscription->vs_id;
        }
        throw new \RuntimeException('Visitor subscription saving error: ' . $subscription->getErrorSummary(true)[0]);
    }

    public function findByUid(string $uid): VisitorSubscription
    {
        if ($subscription = VisitorSubscription::findOne(['vs_subscription_uid' => $uid])) {
            return $subscription;
        }
        throw new NotFoundException('Subscription not found by uid: ' . $uid);
    }
}
