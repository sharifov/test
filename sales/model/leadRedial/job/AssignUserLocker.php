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

    public function lock(int $leadId, \DateTimeImmutable $time): bool
    {
        $microtime = $time->format('U');

        $result = (bool)$this->connection->setnx(self::KEY, $microtime);

        \Yii::info([
            'message' => 'Try to lock for assign users',
            'result' => $result,
            'leadId' => $leadId,
            'microtime' => $microtime,
            'time' => $time->format('Y-m-d H:i:s'),
        ], 'info\AssignUserLocker:lock');

        if ($result === false) {
            $value = $this->connection->get(self::KEY);
            $diff = (int)$microtime - (int)$value;

            \Yii::info([
                'message' => 'Result is false',
                'leadId' => $leadId,
                'storageValue' => $value,
                'currentValue' => $microtime,
                'diffValues' => $diff,
                'time' => $time->format('Y-m-d H:i:s'),
            ], 'info\AssignUserLocker:lock');

            if ($diff <= self::EXPIRE_SECONDS) {
                return false;
            }
            $this->unlock();
            $result = (bool)$this->connection->setnx(self::KEY, $microtime);

            \Yii::info([
                'message' => 'Second try setup lock',
                'leadId' => $leadId,
                'currentValue' => $microtime,
                'result' => $result,
                'diffValues' => $diff,
                'time' => $time->format('Y-m-d H:i:s'),
            ], 'info\AssignUserLocker:lock');

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
