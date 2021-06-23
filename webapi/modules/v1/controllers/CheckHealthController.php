<?php

namespace webapi\modules\v1\controllers;

use webapi\behaviors\HttpBasicAuthCheckHealth;
use webapi\models\ApiCheckHealth;
use yii\rest\Controller;

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
        if ((\Yii::$app->params['apiCheckHealth']['user'] != '') && (\Yii::$app->params['apiCheckHealth']['password'] != '')) {
            $behaviors['authenticator'] = [
                'class' => HttpBasicAuthCheckHealth::class,
//            'auth' => [$this, 'auth']
            ];
        }
            return $behaviors;
    }

//    public function auth($username, $password)
//    {
//        return null;
//    }

    public function actionQuick()
    {
        return ApiCheckHealth::quickCheckHealth();
    }
}
