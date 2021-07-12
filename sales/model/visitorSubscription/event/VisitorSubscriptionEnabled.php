<?php

namespace sales\model\visitorSubscription\event;

/**
 * Class VisitorSubscriptionEnabled
 * @package sales\model\visitorSubscription\event
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
