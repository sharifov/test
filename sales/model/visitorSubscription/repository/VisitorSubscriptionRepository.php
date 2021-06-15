<?php

namespace sales\model\visitorSubscription\repository;

use sales\model\visitorSubscription\entity\VisitorSubscription;

class VisitorSubscriptionRepository
{
    public function save(VisitorSubscription $subscription): int
    {
        if ($subscription->save()) {
            return $subscription->vs_id;
        }
        throw new \RuntimeException('Visitor subscription saving error: ' . $subscription->getErrorSummary(true)[0]);
    }
}
