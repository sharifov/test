<?php

namespace modules\order\controllers;

use frontend\controllers\FController;
use modules\order\src\entities\order\search\OrderQSearch;
use Yii;

class OrderQController extends FController
{
    public function actionNew()
    {
        $searchModel = new OrderQSearch();
        $dataProvider = $searchModel->searchNew(Yii::$app->request->queryParams);

        return $this->render('new', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }
}
