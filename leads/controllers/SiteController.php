<?php

declare(strict_types=1);

namespace leads\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
    public function actionIndex(): Response
    {
        return $this->asJson([
            'ServerName' => Yii::$app->request->serverName,
            'Host' => Yii::$app->params['host'],
            'Date' => date('Y-m-d H:i:s'),
        ]);
    }
}
