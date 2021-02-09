<?php

namespace sales\model\client\entity\events;

use common\models\Client;

/**
 * Class ClientChangeIpEvent
 *
 * @property Client $client
 */
class ClientChangeIpEvent implements ClientEventInterface
{
    public Client $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
