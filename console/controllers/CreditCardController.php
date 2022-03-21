<?php

namespace console\controllers;

use common\models\CreditCard;
use yii\console\Controller;
use yii\console\ExitCode;

class CreditCardController extends Controller
{
    public function actionClearPrivateData()
    {
        CreditCard::updateAll(['cc_cvv' => null, 'cc_number' => null, 'cc_display_number' => null]);
        return ExitCode::OK;
    }
}
