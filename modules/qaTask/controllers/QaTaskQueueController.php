<?php

namespace modules\qaTask\controllers;

use frontend\controllers\FController;
use modules\qaTask\src\entities\qaTask\search\CreateDto;
use modules\qaTask\src\entities\qaTask\search\queue\QaTaskSearchClosedSearch;
use modules\qaTask\src\entities\qaTask\search\queue\QaTaskSearchEscalatedSearch;
use modules\qaTask\src\entities\qaTask\search\queue\QaTaskSearchProcessingSearch;
use modules\qaTask\src\entities\qaTask\search\queue\QaTaskSearchSearch;
use sales\access\ListsAccess;
use sales\access\ProjectAccessService;
use sales\access\QueryAccessService;
use sales\auth\Auth;
use Yii;
use modules\qaTask\src\entities\qaTask\search\queue\QaTaskSearchPendingSearch;
use yii\helpers\ArrayHelper;
use yii\web\Response;

/**
 * Class QaTaskQueueController
 *
 * @property array|null $availableProjects
 * @property array|null $availableUsers
 * @property ProjectAccessService $projectAccessService
 * @property QueryAccessService $queryAccessService
 */
class QaTaskQueueController extends FController
{
    private $availableProjects;
    private $availableUsers;
    private $projectAccessService;
    private $queryAccessService;

    public function __construct(
        $id,
        $module,
        ProjectAccessService $projectAccessService,
        QueryAccessService $queryAccessService,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->projectAccessService = $projectAccessService;
        $this->queryAccessService = $queryAccessService;
    }

    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'count',
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function beforeAction($action): bool
    {
        if ($action->id === 'count') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actionSearch(): string
    {
        $searchModel = QaTaskSearchSearch::createSearch(new CreateDto([
            'user' => Auth::user(),
            'projectList' => $this->getAvailableProjects(),
            'userList' => $this->getAvailableUsers(),
            'queryAccessService' => $this->queryAccessService,
        ]));
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('search', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPending(): string
    {
        $searchModel = QaTaskSearchPendingSearch::createSearch(new CreateDto([
            'user' => Auth::user(),
            'projectList' => $this->getAvailableProjects(),
            'userList' => $this->getAvailableUsers(),
            'queryAccessService' => $this->queryAccessService,
        ]));
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('pending', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionProcessing(): string
    {
        $searchModel = QaTaskSearchProcessingSearch::createSearch(new CreateDto([
            'user' => Auth::user(),
            'projectList' => $this->getAvailableProjects(),
            'userList' => $this->getAvailableUsers(),
            'queryAccessService' => $this->queryAccessService,
        ]));
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('processing', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionEscalated(): string
    {
        $searchModel = QaTaskSearchEscalatedSearch::createSearch(new CreateDto([
            'user' => Auth::user(),
            'projectList' => $this->getAvailableProjects(),
            'userList' => $this->getAvailableUsers(),
            'queryAccessService' => $this->queryAccessService,
        ]));
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('escalated', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionClosed(): string
    {
        $searchModel = QaTaskSearchClosedSearch::createSearch(new CreateDto([
            'user' => Auth::user(),
            'projectList' => $this->getAvailableProjects(),
            'userList' => $this->getAvailableUsers(),
            'queryAccessService' => $this->queryAccessService,
        ]));
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('closed', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCount(): Response
    {
        $types = Yii::$app->request->post('types');

        if (!is_array($types)) {
            return $this->asJson([]);
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

        return $this->asJson($result);
    }

    private function getAvailableProjects(): array
    {
        if ($this->availableProjects !== null) {
            return $this->availableProjects;
        }
        $this->availableProjects = $this->projectAccessService->getProjects(Auth::user());
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
        if (!Auth::can('/qa-task/qa-task-queue/pending')) {
            return null;
        }
        return (QaTaskSearchPendingSearch::createSearch(new CreateDto([
            'user' => Auth::user(),
            'projectList' => $this->getAvailableProjects(),
            'userList' => [],
            'queryAccessService' => $this->queryAccessService,
        ])))->search([])->query->count();
    }

    private function countProcessing(): ?int
    {
        if (!Auth::can('/qa-task/qa-task-queue/processing')) {
            return null;
        }
        return (QaTaskSearchProcessingSearch::createSearch(new CreateDto([
            'user' => Auth::user(),
            'projectList' => $this->getAvailableProjects(),
            'userList' => [],
            'queryAccessService' => $this->queryAccessService,
        ])))->search([])->query->count();
    }

    private function countEscalated(): ?int
    {
        if (!Auth::can('/qa-task/qa-task-queue/escalated')) {
            return null;
        }
        return (QaTaskSearchEscalatedSearch::createSearch(new CreateDto([
            'user' => Auth::user(),
            'projectList' => $this->getAvailableProjects(),
            'userList' => [],
            'queryAccessService' => $this->queryAccessService,
        ])))->search([])->query->count();
    }

    private function countClosed(): ?int
    {
        if (!Auth::can('/qa-task/qa-task-queue/closed')) {
            return null;
        }
        return (QaTaskSearchClosedSearch::createSearch(new CreateDto([
            'user' => Auth::user(),
            'projectList' => $this->getAvailableProjects(),
            'userList' => [],
            'queryAccessService' => $this->queryAccessService,
        ])))->search([])->query->count();
    }
}
