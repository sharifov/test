<?php

namespace modules\qaTask\controllers;

use common\models\Project;
use frontend\controllers\FController;
use modules\qaTask\src\entities\qaTask\search\CreateDto;
use sales\access\ListsAccess;
use sales\access\QueryAccessService;
use sales\auth\Auth;
use Yii;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\entities\qaTask\search\QaTaskCrudSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * QaTaskCrudController implements the CRUD actions for QaTask model.
 *
 * @property QueryAccessService $queryAccessService
 */
class QaTaskCrudController extends FController
{
    private $queryAccessService;

    public function __construct($id, $module, QueryAccessService $queryAccessService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->queryAccessService = $queryAccessService;
    }

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
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = QaTaskCrudSearch::createSearch(new CreateDto([
            'user' => Auth::user(),
            'projectList' => Project::getList(),
            'userList' => (new ListsAccess(Auth::id()))->getEmployees(),
            'queryAccessService' => $this->queryAccessService,
        ]));
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new QaTask();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->t_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->t_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @return QaTask
     * @throws NotFoundHttpException
     */
    protected function findModel($id): QaTask
    {
        if (($model = QaTask::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
