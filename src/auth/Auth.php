<?php

namespace src\auth;

use Yii;
use common\models\Employee;

class Auth
{
    public static function id(): ?int
    {
        return Yii::$app->user->id ?? null;
    }

    public static function user(): Employee
    {
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $user;
    }

    public static function employeeId(): ?int
    {
        if (Yii::$app instanceof \yii\console\Application) {
            return null;
        }
        $identity = Yii::$app->user->identity;
        return ($identity instanceof Employee) ? $identity->getId() : null;
    }

    public static function isGuest(): bool
    {
        return Yii::$app->user->isGuest;
    }

    public static function can($permissionName, $params = [], $allowCaching = true): bool
    {
        return Yii::$app->user->can($permissionName, $params, $allowCaching);
    }
}
