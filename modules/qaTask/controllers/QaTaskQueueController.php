<?php

namespace modules\qaTask\controllers;

use modules\qaTask\src\entities\qaTask\search\queue\QaTaskQueueClosedSearch;
use modules\qaTask\src\entities\qaTask\search\queue\QaTaskQueueEscalatedSearch;
use modules\qaTask\src\entities\qaTask\search\queue\QaTaskQueueProcessingSearch;
use sales\auth\Auth;
use Yii;
use modules\qaTask\src\entities\qaTask\search\queue\QaTaskQueuePendingSearch;
use yii\web\Controller;
use yii\web\Response;

class QaTaskQueueController extends Controller
{
    public function actionPending(): string
    {
        $searchModel = new QaTaskQueuePendingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('pending', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionProcessing(): string
    {
        $searchModel = new QaTaskQueueProcessingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('processing', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionEscalated(): string
    {
        $searchModel = new QaTaskQueueEscalatedSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('escalated', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionClosed(): string
    {
        $searchModel = new QaTaskQueueClosedSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('closed', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCount(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $types = Yii::$app->request->post('types');

        if (!is_array($types)) {
            return [];
        }

        $result = [];

        foreach ($types as $type) {
            switch ($type) {
                case 'pending':
                    if ($count = $this->countPending()) {
                        $result['pending'] = $count;
                    }
                    break;
                case 'processing':
                    if ($count = $this->countProcessing()) {
                        $result['processing'] = $count;
                    }
                    break;
                case 'escalated':
                    if ($count = $this->countEscalated()) {
                        $result['escalated'] = $count;
                    }
                    break;
                case 'closed':
                    if ($count = $this->countClosed()) {
                        $result['closed'] = $count;
                    }
                    break;
            }
        }

        return $result;
    }

    private function countPending(): ?int
    {
        return (new QaTaskQueuePendingSearch())->search([], Auth::user())->query->count();
    }

    private function countProcessing(): ?int
    {
        return (new QaTaskQueueProcessingSearch())->search([], Auth::user())->query->count();
    }

    private function countEscalated(): ?int
    {
        return (new QaTaskQueueEscalatedSearch())->search([], Auth::user())->query->count();
    }

    private function countClosed(): ?int
    {
        return (new QaTaskQueueClosedSearch())->search([], Auth::user())->query->count();
    }
}
