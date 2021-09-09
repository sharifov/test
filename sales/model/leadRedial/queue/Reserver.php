<?php

namespace sales\model\leadRedial\queue;

use yii\redis\Connection;

/**
 * Class CallReserver
 *
 * @property Connection $connection
 */
class Reserver
{
    private const EXPIRE_SECONDS = 20;

    private $connection;

    public function __construct()
    {
        $this->connection = \Yii::$app->redis;
    }

    public function reserve(Key $key, int $userId): bool
    {
        $result = (bool)$this->connection->setnx($key->getValue(), $userId);
        if (!$result) {
            return false;
        }
        $this->connection->expire($key->getValue(), self::EXPIRE_SECONDS);
        return true;
    }

    public function getReservedUser(Key $key): ?int
    {
        $userId = $this->connection->get($key->getValue());
        return $userId ? (int)$userId : null;
    }

    public function isReserved(Key $key): bool
    {
        return $this->connection->exists($key->getValue());
    }

    public function reset(Key $key): void
    {
        $this->connection->del($key->getValue());
    }
}
