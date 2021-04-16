<?php

namespace modules\order\controllers;

use frontend\controllers\FController;
use modules\order\src\entities\order\search\OrderQSearch;
use sales\auth\Auth;
use Yii;

class OrderQController extends FController
{
    public function actionNew()
    {
        $searchModel = new OrderQSearch();
        $dataProvider = $searchModel->searchNew(Yii::$app->request->queryParams, Auth::user());

        return $this->render('new', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionPending()
    {
        $searchModel = new OrderQSearch();
        $dataProvider = $searchModel->searchPending(Yii::$app->request->queryParams, Auth::user());

        return $this->render('pending', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionProcessing()
    {
        $searchModel = new OrderQSearch();
        $dataProvider = $searchModel->searchProcessing(Yii::$app->request->queryParams, Auth::user());

        return $this->render('processing', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionPrepared()
    {
        $searchModel = new OrderQSearch();
        $dataProvider = $searchModel->searchPrepared(Yii::$app->request->queryParams, Auth::user());

        return $this->render('prepared', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionComplete()
    {
        $searchModel = new OrderQSearch();
        $dataProvider = $searchModel->searchComplete(Yii::$app->request->queryParams, Auth::user());

        return $this->render('complete', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionCancelProcessing()
    {
        $searchModel = new OrderQSearch();
        $dataProvider = $searchModel->searchCancelProcessing(Yii::$app->request->queryParams, Auth::user());

        return $this->render('cancel-processing', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionError()
    {
        $searchModel = new OrderQSearch();
        $dataProvider = $searchModel->searchError(Yii::$app->request->queryParams, Auth::user());

        return $this->render('error', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionDeclined()
    {
        $searchModel = new OrderQSearch();
        $dataProvider = $searchModel->searchDeclined(Yii::$app->request->queryParams, Auth::user());

        return $this->render('declined', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionCanceled()
    {
        $searchModel = new OrderQSearch();
        $dataProvider = $searchModel->searchCanceled(Yii::$app->request->queryParams, Auth::user());

        return $this->render('canceled', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionCancelFailed()
    {
        $searchModel = new OrderQSearch();
        $dataProvider = $searchModel->searchCancelFailed(Yii::$app->request->queryParams, Auth::user());

        return $this->render('cancel-failed', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }
}
