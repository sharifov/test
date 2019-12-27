<?php

namespace sales\repositories\client;

use common\models\Client;
use sales\dispatchers\EventDispatcher;
use sales\model\client\ClientCodeException;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

/**
 * Class ClientRepository
 *
 * @method null|Client get($id)
 */
class ClientRepository extends Repository
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
}
