<?php

namespace src\repositories\client;

use common\models\Client;
use src\dispatchers\EventDispatcher;
use src\model\client\ClientCodeException;
use src\repositories\NotFoundException;

/**
 * Class ClientRepository
 */
class ClientRepository
{
    private $eventDispatcher;

    /**
     * ClientRepository constructor.
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $id
     * @return Client
     */
    public function find($id): Client
    {
        if ($client = Client::findOne($id)) {
            return $client;
        }
        throw new NotFoundException('Client is not found', ClientCodeException::CLIENT_NOT_FOUND);
    }

    /**
     * @param Client $client
     * @return int
     */
    public function save(Client $client): int
    {
        if (!$client->save(false)) {
            throw new \RuntimeException('Saving error', ClientCodeException::CLIENT_SAVE);
        }
        $this->eventDispatcher->dispatchAll($client->releaseEvents());
        return $client->id;
    }

    /**
     * @param Client $client
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(Client $client): void
    {
        if (!$client->delete()) {
            throw new \RuntimeException('Removing error', ClientCodeException::CLIENT_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($client->releaseEvents());
    }

    /**
     * @param string $uuid
     * @param int $projectId
     * @return Client
     */
    public function findByUuidAndProjectId(string $uuid, int $projectId): Client
    {
        if ($client = Client::findOne(['uuid' => $uuid, 'cl_project_id' => $projectId])) {
            return $client;
        }
        throw new NotFoundException('Client is not found', ClientCodeException::CLIENT_NOT_FOUND);
    }
}
