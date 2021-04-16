<?php

namespace modules\order\controllers;

use frontend\controllers\FController;
use modules\order\src\entities\order\OrderStatus;
use modules\order\src\entities\order\search\OrderQSearch;
use sales\auth\Auth;
use Yii;
use yii\helpers\Json;

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

    public function actionGetBadgesCount(): string
    {
        $types = Yii::$app->request->post('types');

        $searchModel = new OrderQSearch();

        if (!is_array($types)) {
            return Json::encode([]);
        }

        $result = [];

        foreach ($types as $type) {
            switch ($type) {
                case 'new':
                    if ($count = $searchModel->ordersCounter(OrderStatus::NEW)) {
                        $result['new'] = $count;
                    }
                    break;
                case 'pending':
                    if ($count = $searchModel->ordersCounter(OrderStatus::PENDING)) {
                        $result['pending'] = $count;
                    }
                    break;
                case 'processing':
                    if ($count = $searchModel->ordersCounter(OrderStatus::PROCESSING)) {
                        $result['processing'] = $count;
                    }
                    break;
                case 'prepared':
                    if ($count = $searchModel->ordersCounter(OrderStatus::PREPARED)) {
                        $result['prepared'] = $count;
                    }
                    break;
                case 'complete':
                    if ($count = $searchModel->ordersCounter(OrderStatus::COMPLETE)) {
                        $result['complete'] = $count;
                    }
                    break;
                case 'cancel-processing':
                    if ($count = $searchModel->ordersCounter(OrderStatus::CANCEL_PROCESSING)) {
                        $result['cancel-processing'] = $count;
                    }
                    break;
                case 'error':
                    if ($count = $searchModel->ordersCounter(OrderStatus::ERROR)) {
                        $result['error'] = $count;
                    }
                    break;
                case 'declined':
                    if ($count = $searchModel->ordersCounter(OrderStatus::DECLINED)) {
                        $result['declined'] = $count;
                    }
                    break;
                case 'canceled':
                    if ($count = $searchModel->ordersCounter(OrderStatus::CANCELED)) {
                        $result['canceled'] = $count;
                    }
                    break;
                case 'canceled-failed':
                    if ($count = $searchModel->ordersCounter(OrderStatus::CANCEL_FAILED)) {
                        $result['canceled-failed'] = $count;
                    }
                    break;
            }
        }

        return Json::encode($result);
    }
}
