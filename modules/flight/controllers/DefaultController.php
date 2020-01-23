<?php

namespace modules\flight\controllers;

use frontend\controllers\FController;
use modules\flight\FlightModule;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * Default controller for the `flight` module
 */
class DefaultController extends FController
{
//    /**
//     * @return array
//     */
//    public function behaviors(): array
//    {
//        $behaviors = [
//            'verbs' => [
//                'class' => VerbFilter::class,
//                'actions' => [
//                    'delete' => ['POST'],
//                ],
//            ],
//        ];
//        return ArrayHelper::merge(parent::behaviors(), $behaviors);
//    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $links = FlightModule::getListMenu();
        return $this->render('index', ['links' => $links]);
    }
}
