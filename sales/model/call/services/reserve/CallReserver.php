<?php

namespace sales\model\call\services\reserve;

use yii\redis\Connection;

/**
 * Class CallReserver
 *
 * @property Connection $redis
 */
class CallReserver
{
    private const EXPIRE_SECONDS = 20;

    private $redis;

    public function __construct()
    {
        $this->redis = \Yii::$app->redis;
    }

    public function reserve(Key $key, int $userId): bool
    {
        $result = (bool)$this->redis->setnx($key->getValue(), $userId);
        if (!$result) {
            return false;
        }
        $this->redis->expire($key->getValue(), self::EXPIRE_SECONDS);
        return true;
    }

    public function getReservedUser(Key $key): ?int
    {
        $userId = $this->redis->get($key->getValue());
        return $userId ? (int)$userId : null;
    }
}
