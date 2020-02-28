<?php

namespace frontend\controllers;

use Yii;
use sales\model\kpi\entity\KpiUserPerformance;
use sales\model\kpi\entity\search\KpiUserPerformanceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * KpiUserPerformanceCrudController implements the CRUD actions for KpiUserPerformance model.
 */
class KpiUserPerformanceCrudController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all KpiUserPerformance models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new KpiUserPerformanceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single KpiUserPerformance model.
     * @param integer $up_user_id
     * @param integer $up_year
     * @param integer $up_month
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($up_user_id, $up_year, $up_month)
    {
        return $this->render('view', [
            'model' => $this->findModel($up_user_id, $up_year, $up_month),
        ]);
    }

    /**
     * Creates a new KpiUserPerformance model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new KpiUserPerformance();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'up_user_id' => $model->up_user_id, 'up_year' => $model->up_year, 'up_month' => $model->up_month]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing KpiUserPerformance model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $up_user_id
     * @param integer $up_year
     * @param integer $up_month
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($up_user_id, $up_year, $up_month)
    {
        $model = $this->findModel($up_user_id, $up_year, $up_month);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'up_user_id' => $model->up_user_id, 'up_year' => $model->up_year, 'up_month' => $model->up_month]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing KpiUserPerformance model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $up_user_id
     * @param integer $up_year
     * @param integer $up_month
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($up_user_id, $up_year, $up_month)
    {
        $this->findModel($up_user_id, $up_year, $up_month)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the KpiUserPerformance model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $up_user_id
     * @param integer $up_year
     * @param integer $up_month
     * @return KpiUserPerformance the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($up_user_id, $up_year, $up_month)
    {
        if (($model = KpiUserPerformance::findOne(['up_user_id' => $up_user_id, 'up_year' => $up_year, 'up_month' => $up_month])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
