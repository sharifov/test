<?php

namespace src\model\visitorSubscription\event;

/**
 * Class VisitorSubscriptionEnabled
 * @package src\model\visitorSubscription\event
 *
 * @property string $visitorUid
 */
class VisitorSubscriptionEnabled
{
    public string $visitorUid;

    public function __construct(string $visitorUid)
    {
        $this->visitorUid = $visitorUid;
    }
}
