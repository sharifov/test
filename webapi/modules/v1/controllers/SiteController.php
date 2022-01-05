<?php

namespace webapi\modules\v1\controllers;

use Yii;
use yii\filters\ContentNegotiator;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        return $behaviors;
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        echo  '<h1>API v1 - ' . Yii::$app->request->serverName . '</h1> ' . date('Y-m-d H:i:s');
        exit;
    }

    /**
     * @return array
     */
    public function actionTest(): array
    {

        $start_microtime = microtime(true);
        $start_memory_usage = memory_get_usage();

        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (strpos($name, 'HTTP_') === 0) {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        $out = [
            'message'   => 'Server Name: ' . Yii::$app->request->serverName,
            'code'      => 0,
            'datetime'      => date('Y-m-d H:i:s'),
            'ip'        => Yii::$app->request->getUserIP(),
            'remoteIP'  => Yii::$app->request->getRemoteIP(),
            'get'       => Yii::$app->request->get(),
            'post'      => Yii::$app->request->post(),
            'files'     => $_FILES,
            'headers'   => $headers
        ];

        if ($delay = Yii::$app->request->get('delay')) {
            $delay = (float) $delay;
            usleep(round($delay * 1000000));
            $out['delay_seconds'] = $delay;
        }

        $end_microtime = microtime(true);
        $end_memory_usage = memory_get_usage();

        if ($start_microtime) {
            $time = round($end_microtime - $start_microtime, 3);
        } else {
            $time = 0;
        }

        if ($time > 999) {
            $time = 999;
        }

        if ($start_memory_usage) {
            $memory_usage = $end_memory_usage - $start_memory_usage;
        } else {
            $memory_usage = 0;
        }


        //VarDumper::dump($time);exit;

        $out['execution_time'] = $time;
        $out['memory_usage'] = Yii::$app->formatter->asShortSize($memory_usage);
        $out['memory_peak_usage'] = Yii::$app->formatter->asShortSize(memory_get_peak_usage());

        Yii::warning(VarDumper::dumpAsString($out), 'info\API:v1:AppController:Test');
        //VarDumper::dump($out);
        return $out;
    }
}
