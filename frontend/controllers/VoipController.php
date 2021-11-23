<?php

namespace frontend\controllers;

use yii\web\Controller;

class VoipController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
