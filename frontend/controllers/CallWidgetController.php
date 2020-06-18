<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\web\Controller;


/**
 * Class CallWidgetController
 * @package frontend\controllers
 */
class CallWidgetController extends Controller
{


    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    //'cancel' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function actionIndex(): string
    {
        return $this->renderAjax('index', [
        ]);
    }

}
