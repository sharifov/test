<?php

namespace frontend\controllers;

use src\services\cleaner\form\DbCleanerParamsForm;
use src\services\cleaner\cleaners\UserMonitorCleaner;
use Yii;
use src\model\user\entity\monitor\UserMonitor;
use src\model\user\entity\monitor\search\UserMonitorSearch;
use frontend\controllers\FController;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserMonitorController implements the CRUD actions for UserMonitor model.
 */
class UserMonitorController extends FController
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

    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    /**
     * Lists all UserMonitor models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserMonitorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $userMonitorCleaner = new UserMonitorCleaner();
        $dbCleanerParamsForm = (new DbCleanerParamsForm())
            ->setTable($userMonitorCleaner->getTable())
            ->setColumn($userMonitorCleaner->getColumn());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'modelCleaner' => $dbCleanerParamsForm,
        ]);
    }

    /**
     * @return string
     */
    public function actionStats(): string
    {
        $searchModel = new UserMonitorSearch();

        $startDateTime = date('Y-m-d H:i', strtotime('-1 day'));
        $endDateTime = date('Y-m-d H:i', strtotime('+10 hours'));

        $params = Yii::$app->request->queryParams;

        if (!empty($params['UserMonitorSearch']['startTime']) && !empty($params['UserMonitorSearch']['endTime'])) {
            $startDateTime = $params['UserMonitorSearch']['startTime'];
            $endDateTime = $params['UserMonitorSearch']['endTime'];
        }

        $data = $searchModel->searchStats($params, $startDateTime);

        $pagination = new Pagination([
            'totalCount' => count($data['users']),
            'pageSize' => 10
        ]);

        if (Yii::$app->request->isPjax) {
            if ($params['page'] == 1) {
                $pageOffset = 0;
            } else {
                $pageOffset = $params['page'] * $pagination->getPageSize() - $pagination->getPageSize();
            }

            $dataPager = array_slice($data['users'], $pageOffset, $pagination->getPageSize(), true);
            unset($data['users']);
            $data['users'] = $dataPager;
            return $this->renderAjax('stats', [
                'data' => $data,
                'searchModel' => $searchModel,
                'startDateTime' => $startDateTime,
                'endDateTime' => $endDateTime,
                'pagination' => $pagination,
                'pageOffset' => $pageOffset
            ]);
        } else {
            if (isset($params['page'])) {
                if ($params['page'] == 1) {
                    $pageOffset = 0;
                } else {
                    $pageOffset = $params['page'] * $pagination->getPageSize() - $pagination->getPageSize();
                }
            } else {
                $pageOffset = 0;
            }
            $dataPager = array_slice($data['users'], $pageOffset, $pagination->getPageSize(), true);
            unset($data['users']);
            $data['users'] = $dataPager;

            return $this->render('stats', [
                'data' => $data,
                'searchModel' => $searchModel,
                'startDateTime' => $startDateTime,
                'endDateTime' => $endDateTime,
                'pagination' => $pagination,
                'pageOffset' => $pageOffset
            ]);
        }
    }

    /**
     * Displays a single UserMonitor model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new UserMonitor model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserMonitor();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->um_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserMonitor model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->um_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserMonitor model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserMonitor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserMonitor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserMonitor::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
