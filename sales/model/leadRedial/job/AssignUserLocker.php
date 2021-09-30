<?php

namespace sales\model\leadRedial\job;

use yii\redis\Connection;

/**
 * Class Locker
 *
 * @property Connection $connection
 */
class AssignUserLocker
{
    private const KEY = 'lead_redial_assign_user';

    private const EXPIRE_SECONDS = 20;

    private $connection;

    public function __construct()
    {
        $this->connection = \Yii::$app->redis;
    }

    public function lock(): bool
    {
        $currentTime = time();
        $result = (bool)$this->connection->setnx(self::KEY, $currentTime);
        if ($result === false) {
            $value = $this->connection->get(self::KEY);
            if (($currentTime - (int)$value) <= self::EXPIRE_SECONDS) {
                return false;
            }
            $this->unlock();
            $result = (bool)$this->connection->setnx(self::KEY, $currentTime);
            if ($result === false) {
                return false;
            }
        }
        $this->connection->expire(self::KEY, self::EXPIRE_SECONDS);
        return true;
    }

    public function unlock(): void
    {
        $this->connection->del(self::KEY);
    }
}
