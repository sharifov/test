<?php

namespace sales\model\client\entity\events;

use common\models\Client;

/**
 * Class ClientChangeIpEvent
 *
 * @property Client $client
 */
class ClientChangeIpEvent
{
    public Client $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}
