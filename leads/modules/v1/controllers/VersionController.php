<?php

declare(strict_types=1);

namespace leads\modules\v1\controllers;

use yii\web\Controller;

class VersionController extends Controller
{
    public function actionInfo(): array
    {
        return [
            'version' => 'v1'
        ];
    }
}
