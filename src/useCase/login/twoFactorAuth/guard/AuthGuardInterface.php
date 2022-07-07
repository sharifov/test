<?php

namespace src\useCase\login\twoFactorAuth\guard;

use common\models\Employee;

interface AuthGuardInterface
{
    public function guardMethod(Employee $user): bool;
}
