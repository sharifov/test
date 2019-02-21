<?php

namespace frontend\controllers;

use common\models\UserProjectParams;
use yii\helpers\VarDumper;
use \yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use common\CommunicationService;
use yii\web\View;

class PhoneController extends FController
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $this->layout = false;

        $user = \Yii::$app->user->identity;
        /*$params = UserProjectParams::find(['upp_user_id' => $user->id])->all();
        $tw_number = '';
        if(count($params)) {
            foreach ($params AS $param) {
                if(strlen($param->upp_tw_phone_number) > 7) {
                    $tw_number = $param->upp_tw_phone_number;
                    break;
                }
            }
        }*/

        $tw_number = '+15596489977';

        return $this->render('index', [
            'client' => $this->filterClientUsername($user->username),
            'fromAgentPhone' => $tw_number,
        ]);
    }

    public function actionGetToken()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $username = $this->filterClientUsername(\Yii::$app->user->identity->username);
        //VarDumper::dump($username, 10, true); exit;
        $data = Yii::$app->communication->getJwtTokenCache($username, true);
        return $data;
    }

    protected function filterClientUsername($string)
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', $string);
    }

}
