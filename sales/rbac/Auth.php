<?php

namespace sales\rbac;

use Yii;
use common\models\Employee;

class Auth
{
    public static function Id(): int
    {
        return Yii::$app->user->id;
    }

    public static function Identity(): Employee
    {
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $user;
    }
}
