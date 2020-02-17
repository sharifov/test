<?php

namespace modules\qaTask\controllers;

use frontend\controllers\FController;
use modules\qaTask\src\entities\qaTask\search\CreateDto;
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
 * @property array|null $availableUsers
 */
class QaTaskQueueController extends FController
{
    private $availableProjects;
    private $availableUsers;

    public function beforeAction($action): bool
    {
        if ($action->id === 'count') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actionSearch(): string
    {
        $searchModel = QaTaskSearchSearch::create(new CreateDto([
            'user' => Auth::user(),
            'projectList' => $this->getAvailableProjects(),
            'userList' => $this->getAvailableUsers(),
        ]));
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('search', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPending(): string
    {
        $searchModel = QaTaskSearchPendingSearch::create(new CreateDto([
            'user' => Auth::user(),
            'projectList' => $this->getAvailableProjects(),
            'userList' => $this->getAvailableUsers(),
        ]));
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('pending', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionProcessing(): string
    {
        $searchModel = QaTaskSearchProcessingSearch::create(new CreateDto([
            'user' => Auth::user(),
            'projectList' => $this->getAvailableProjects(),
            'userList' => $this->getAvailableUsers(),
        ]));
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('processing', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionEscalated(): string
    {
        $searchModel = QaTaskSearchEscalatedSearch::create(new CreateDto([
            'user' => Auth::user(),
            'projectList' => $this->getAvailableProjects(),
            'userList' => $this->getAvailableUsers(),
        ]));
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('escalated', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionClosed(): string
    {
        $searchModel = QaTaskSearchClosedSearch::create(new CreateDto([
            'user' => Auth::user(),
            'projectList' => $this->getAvailableProjects(),
            'userList' => $this->getAvailableUsers(),
        ]));
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

    private function getAvailableUsers(): array
    {
        if ($this->availableUsers !== null) {
            return $this->availableUsers;
        }
        $this->availableUsers = (new ListsAccess(Auth::id()))->getEmployees();
        return $this->availableUsers;
    }

    private function countPending(): ?int
    {
        return (QaTaskSearchPendingSearch::create(new CreateDto([
            'user' => Auth::user(),
            'projectList' => $this->getAvailableProjects(),
            'userList' => [],
        ])))->search([])->query->count();
    }

    private function countProcessing(): ?int
    {
        return (QaTaskSearchProcessingSearch::create(new CreateDto([
            'user' => Auth::user(),
            'projectList' => $this->getAvailableProjects(),
            'userList' => [],
        ])))->search([])->query->count();
    }

    private function countEscalated(): ?int
    {
        return (QaTaskSearchEscalatedSearch::create(new CreateDto([
            'user' => Auth::user(),
            'projectList' => $this->getAvailableProjects(),
            'userList' => [],
        ])))->search([])->query->count();
    }

    private function countClosed(): ?int
    {
        return (QaTaskSearchClosedSearch::create(new CreateDto([
            'user' => Auth::user(),
            'projectList' => $this->getAvailableProjects(),
            'userList' => [],
        ])))->search([])->query->count();
    }
}
