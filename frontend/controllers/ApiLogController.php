<?php

namespace frontend\controllers;

use src\services\cleaner\cleaners\ApiLogCleaner;
use src\services\cleaner\form\DbCleanerParamsForm;
use Yii;
use common\models\ApiLog;
use common\models\search\ApiLogSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ApiLogController implements the CRUD actions for ApiLog model.
 */
class ApiLogController extends FController
{
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
     * Lists all ApiLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ApiLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $cleaner = new ApiLogCleaner();
        $dbCleanerParamsForm = (new DbCleanerParamsForm())
            ->setTable($cleaner->getTable())
            ->setColumn($cleaner->getColumn());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'modelCleaner' => $dbCleanerParamsForm,
        ]);
    }

    /**
     * Lists of ApiLog Action.
     * @return array
     */
    public function actionAjaxActionList($timeRange = null): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['error' => '', 'data' => []];
        $result['data'] = ApiLog::getActionFilterByCnt($timeRange ?: null);
        return $result;
    }

    /**
     * Displays a single ApiLog model.
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
     * Creates a new ApiLog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ApiLog();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->al_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ApiLog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->al_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ApiLog model.
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

    public function actionDeleteAll()
    {
        ApiLog::getDb()->createCommand()->truncateTable(ApiLog::tableName())->execute();
        return $this->redirect(['index']);
    }

    /**
     * Finds the ApiLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ApiLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ApiLog::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
