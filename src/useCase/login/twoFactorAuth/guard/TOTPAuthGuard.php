<?php

namespace src\useCase\login\twoFactorAuth\guard;

use common\models\Employee;
use src\useCase\login\twoFactorAuth\abac\TwoFactorAuthAbacObject;

class TOTPAuthGuard implements AuthGuardInterface
{
    public function guardMethod(Employee $user): bool
    {
        return \Yii::$app->abac->can(null, TwoFactorAuthAbacObject::TWO_FACTOR_AUTH, TwoFactorAuthAbacObject::ACTION_TOTP, $user);
    }
}
