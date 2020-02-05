<?php

namespace modules\hotel\controllers;

use frontend\controllers\FController;
use modules\hotel\src\entities\hotelQuoteServiceLog\HotelQuoteServiceLog;
use modules\hotel\src\entities\hotelQuoteServiceLog\search\HotelQuoteServiceLogCrudSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class HotelQuoteServiceLogController extends FController
{
    /**
     * @return array
     */
    public function behaviors()
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

    public function actionHotelQuoteLog()
    {
        $hotelQuoteId = Yii::$app->request->get('id', 0);
        $searchModel = new HotelQuoteServiceLogCrudSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $hotelQuoteId);

        return $this->renderAjax('hotel_quote_log', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
