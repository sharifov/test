<?php
namespace webapi\modules\v1\controllers;

use Yii;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\HttpException;

/**
 * Twilio controller
 */
class TwilioController extends Controller
{


    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        echo  '<h1>API - Twilio - '.Yii::$app->request->serverName.'</h1> '.date('Y-m-d H:i:s');
        exit;
    }

    public function actionCallback()
    {

        $headers = [];
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        $out = [
            'message'   => 'Server Name: '.Yii::$app->request->serverName,
            'date'      => date('Y-m-d'),
            'time'      => date('H:i:s'),
            'ip'        => Yii::$app->request->getUserIP(),
            'get'       => Yii::$app->request->get(),
            'post'      => Yii::$app->request->post(),
            'files'     => $_FILES,
            'headers'   => $headers
        ];

        Yii::warning(VarDumper::dumpAsString($out), 'Twilio Callback');

    }

    public function actionRequest()
    {

        $headers = [];
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        $out = [
            'message'   => 'Server Name: '.Yii::$app->request->serverName,
            'date'      => date('Y-m-d'),
            'time'      => date('H:i:s'),
            'ip'        => Yii::$app->request->getUserIP(),
            'get'       => Yii::$app->request->get(),
            'post'      => Yii::$app->request->post(),
            'files'     => $_FILES,
            'headers'   => $headers
        ];

        Yii::warning(VarDumper::dumpAsString($out), 'Twilio Request');

    }

    public function actionFallback()
    {


        $headers = [];
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        $out = [
            'message'   => 'Server Name: '.Yii::$app->request->serverName,
            'date'      => date('Y-m-d'),
            'time'      => date('H:i:s'),
            'ip'        => Yii::$app->request->getUserIP(),
            'get'       => Yii::$app->request->get(),
            'post'      => Yii::$app->request->post(),
            'files'     => $_FILES,
            'headers'   => $headers
        ];

        Yii::warning(VarDumper::dumpAsString($out), 'Twilio Fallback');

    }

}