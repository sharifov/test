<?php

namespace frontend\controllers;

use src\model\lead\reports\HeatMapLeadSearch;

/**
 * Class HeatMapLeadController
 */
class HeatMapLeadController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    public function actionIndex()
    {
        $searchModel = new HeatMapLeadSearch(); /* TODO::  */
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
