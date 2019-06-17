<?php

namespace sales\repositories\client;

use common\models\Client;
use sales\repositories\NotFoundException;

class ClientRepository
{
    public function get($id): Client
    {
        if ($client = Client::findOne($id)) {
            return $client;
        }
        throw new NotFoundException('Client is not found');
    }

    public function save(Client $client): int
    {
        if ($client->save(false)) {
            return $client->id;
        }
        throw new \RuntimeException('Saving error');
    }

    public function remove(Client $client): void
    {
        if (!$client->delete()) {
            throw new \RuntimeException('Removing error');
        }
    }
}