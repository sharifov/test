<?php

namespace webapi\modules\v1\controllers;

use webapi\behaviors\HttpBasicAuthCheckHealth;
use yii\rest\Controller;
use yii\helpers\VarDumper;

class CheckHealthController extends Controller
{

    public function init()
    {
        parent::init();

        \Yii::$app->user->enableSession = false;
        if (\Yii::$app->request->get('debug')) {
            $this->debug = true;
        }
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuthCheckHealth::class,
        ];
        return $behaviors;
    }

    public function actionQuick()
    {
        $response = [];
        try {
            $test_row = \Yii::$app->db->createCommand('SELECT id FROM employees LIMIT 1')->queryOne();
            $response['mysql'] = boolval($test_row > 0);
        } catch (\Throwable $e) {
            $response['mysql'] = false;
        }

        try {
            $test_row = \Yii::$app->db_postgres->createCommand('SELECT id from log LIMIT 1')->queryAll();
            $response['postgresql'] = boolval($test_row > 0);
        } catch (\Throwable $e) {
            $response['postgresql'] = false;
        }

        try {
            $redis = \Yii::$app->redis;
            $key = 'health_check';
            $redis->set($key, 'test_passed');
            if ($redis->get($key) == 'test_passed') {
                $response['redis'] = true;
            } else {
                $response['redis'] = false;
            }
        } catch (\Throwable $e) {
            $response['redis'] = false;
        }

        try {
            $communication = \Yii::$app->communication;
            $comRequest = $communication->phoneNumberList(1, 1);
            if (!$comRequest || !empty($comRequest['error'])) {
                $response['communication'] = false;
            } else {
                $response['communication'] = true;
            }
        } catch (\Throwable $e) {
            $response['communication'] = false;
        }


        return $response;
    }
}
