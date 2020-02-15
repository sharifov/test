<?php

namespace modules\qaTask\controllers;

use frontend\controllers\FController;
use modules\qaTask\src\entities\qaTask\search\queue\QaTaskSearchClosedSearch;
use modules\qaTask\src\entities\qaTask\search\queue\QaTaskSearchEscalatedSearch;
use modules\qaTask\src\entities\qaTask\search\queue\QaTaskSearchProcessingSearch;
use modules\qaTask\src\entities\qaTask\search\queue\QaTaskSearchSearch;
use sales\access\ListsAccess;
use sales\auth\Auth;
use Yii;
use modules\qaTask\src\entities\qaTask\search\queue\QaTaskSearchPendingSearch;
use yii\web\Response;

/**
 * Class QaTaskQueueController
 *
 * @property array|null $availableProjects
 */
class QaTaskQueueController extends FController
{
    private $availableProjects;

    public function beforeAction($action): bool
    {
        if ($action->id === 'count') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actionSearch(): string
    {
        $searchModel = new QaTaskSearchSearch(Auth::user(), $this->getAvailableProjects());
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('search', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPending(): string
    {
        $searchModel = new QaTaskSearchPendingSearch(Auth::user(), $this->getAvailableProjects());
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('pending', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionProcessing(): string
    {
        $searchModel = new QaTaskSearchProcessingSearch(Auth::user(), $this->getAvailableProjects());
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('processing', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionEscalated(): string
    {
        $searchModel = new QaTaskSearchEscalatedSearch(Auth::user(), $this->getAvailableProjects());
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('escalated', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionClosed(): string
    {
        $searchModel = new QaTaskSearchClosedSearch(Auth::user(), $this->getAvailableProjects());
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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

    private function getAvailableProjects(): array
    {
        if ($this->availableProjects !== null) {
            return $this->availableProjects;
        }
        $this->availableProjects = (new ListsAccess(Auth::id()))->getProjects();
        return $this->availableProjects;
    }

    private function countPending(): ?int
    {
        return (new QaTaskSearchPendingSearch(Auth::user(), $this->getAvailableProjects()))->search([])->query->count();
    }

    private function countProcessing(): ?int
    {
        return (new QaTaskSearchProcessingSearch(Auth::user(), $this->getAvailableProjects()))->search([])->query->count();
    }

    private function countEscalated(): ?int
    {
        return (new QaTaskSearchEscalatedSearch(Auth::user(), $this->getAvailableProjects()))->search([])->query->count();
    }

    private function countClosed(): ?int
    {
        return (new QaTaskSearchClosedSearch(Auth::user(), $this->getAvailableProjects()))->search([])->query->count();
    }
}
