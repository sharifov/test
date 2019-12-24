<?php

namespace modules\hotel\controllers;

use frontend\controllers\FController;
use modules\hotel\HotelModule;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * Default controller for the `hotel` module
 */
class DefaultController extends FController
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $links = HotelModule::getListMenu();
        return $this->render('index', ['links' => $links]);
    }
}
