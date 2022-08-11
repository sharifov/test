<?php

namespace src\useCase\login\twoFactorAuth\viewHelper;

use common\models\Employee;
use yii\widgets\ActiveForm;

interface TwoFactorViewHelperInterface
{
    public function renderView(ActiveForm $form, Employee $user): string;
}
