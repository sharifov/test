<?php

namespace sales\model\client\entity\events;

use common\models\Client;

/**
 * Class ClientCreatedEvent
 *
 * @property Client $client
 */
class ClientCreatedEvent
{
    public Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}
