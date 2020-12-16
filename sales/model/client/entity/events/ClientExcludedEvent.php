<?php

namespace sales\model\client\entity\events;

/**
 * Class ClientExcludedEvent
 *
 * @property int $clientId
 */
class ClientExcludedEvent
{
    public int $clientId;

    public function __construct(int $clientId)
    {
        $this->clientId = $clientId;
    }
}
