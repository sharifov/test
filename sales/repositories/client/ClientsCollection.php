<?php

namespace sales\repositories\client;

use common\models\Client;

/**
 * Class ClientsCollection
 *
 * @property Client[] $clients
 */
class ClientsCollection
{
    private array $clients;

    public function __construct(array $clients)
    {
        $this->clients = $clients;
    }

    public function getWithProject(int $project): ?Client
    {
        foreach ($this->clients as $client) {
            if ($client->isProjectEqual($project)) {
                return $client;
            }
        }
        return null;
    }

    public function getWithoutProject(): ?Client
    {
        foreach ($this->clients as $client) {
            if ($client->isWithoutProject()) {
                return $client;
            }
        }
        return null;
    }

    public function getFirstId(): ?int
    {
        $client = reset($this->clients);
        if (!$client) {
            return null;
        }
        return $client->id;
    }

    public function isEmpty(): bool
    {
        return empty($this->clients);
    }

    public function getIds(): array
    {
        return array_column($this->clients, 'id');
    }
}
