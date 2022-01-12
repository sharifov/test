<?php

namespace src\model\userData\entity;

class UserDataQuery
{
    public static function insertOrUpdate(int $userId, int $key, string $value, \DateTimeImmutable $updatedDt): int
    {
        return UserData::getDb()->createCommand(
            "insert into " . UserData::tableName() . " (`ud_user_id`, `ud_key`, `ud_value`, `ud_updated_dt`) values (:value1, :value2, :value3, :value4) on duplicate key update ud_user_id = :value1, ud_key = :value2, ud_value = :value3, ud_updated_dt = :value4",
            [
                ':value1' => $userId,
                ':value2' => $key,
                ':value3' => $value,
                ':value4' => $updatedDt->format('Y-m-d H:i:s')
            ]
        )->execute();
    }
}
