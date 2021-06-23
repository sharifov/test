<?php

namespace webapi\modules\v1\controllers;

use webapi\behaviors\HttpBasicAuthCheckHealth;
use webapi\models\ApiCheckHealth;
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
        return ApiCheckHealth::quickCheckHealth();
    }
}
