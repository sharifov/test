<?php

declare(strict_types=1);

namespace leads\controllers;

use Yii;
use yii\web\Controller;

class SiteController extends Controller
{
    public function actionIndex(): array
    {
        return [
            'ServerName' => Yii::$app->request->serverName,
            'Date' => date('Y-m-d H:i:s'),
        ];
    }
}
