<?php
namespace webapi\modules\v1\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HttpException;

/**
 * Site controller
 */
class SiteController extends Controller
{


    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        echo  '<h1>API - '.Yii::$app->request->serverName.'</h1> '.date('Y-m-d H:i:s');
        exit;
    }

    public function actionTestLog()
    {
        Yii::error('error '.print_r($_SERVER, true), 'WEBAPI test');
        Yii::warning('warning' . print_r($_SERVER, true), 'WEBAPI test');
        Yii::info('info' . print_r($_SERVER, true), 'WEBAPI test');
        Yii::debug('trace' . print_r($_SERVER, true), 'WEBAPI test');

        throw new HttpException(422, 'Test API HttpException 422');
    }

}