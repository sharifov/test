<?php

namespace src\useCase\login\twoFactorAuth\forms;

use common\models\Employee;

interface TwoFactorFormInterface
{
    public function setUser(Employee $user): self;

    public function login(bool $rememberMe): bool;
}
