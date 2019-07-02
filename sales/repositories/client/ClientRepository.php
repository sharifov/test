<?php

namespace sales\repositories\client;

use common\models\Client;
use sales\repositories\NotFoundException;

class ClientRepository
{
    /**
     * @param $id
     * @return Client
     */
    public function get($id): Client
    {
        if ($client = Client::findOne($id)) {
            return $client;
        }
        throw new NotFoundException('Client is not found');
    }

    /**
     * @param Client $client
     * @return int
     */
    public function save(Client $client): int
    {
        if ($client->save(false)) {
            return $client->id;
        }
        throw new \RuntimeException('Saving error');
    }

    /**
     * @param Client $client
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(Client $client): void
    {
        if (!$client->delete()) {
            throw new \RuntimeException('Removing error');
        }
    }
}