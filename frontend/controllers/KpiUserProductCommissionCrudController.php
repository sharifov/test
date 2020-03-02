<?php

namespace frontend\controllers;

use Yii;
use sales\model\kpi\entity\kpiUserProductCommission\KpiUserProductCommission;
use sales\model\kpi\entity\kpiUserProductCommission\search\KpiUserProductCommissionSearch;
use frontend\controllers\FController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * KpiUserProductCommissionCrudController implements the CRUD actions for KpiUserProductCommission model.
 */
class KpiUserProductCommissionCrudController extends FController
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
     * Lists all KpiUserProductCommission models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new KpiUserProductCommissionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single KpiUserProductCommission model.
     * @param integer $upc_product_type_id
     * @param integer $upc_user_id
     * @param integer $upc_year
     * @param integer $upc_month
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($upc_product_type_id, $upc_user_id, $upc_year, $upc_month)
    {
        return $this->render('view', [
            'model' => $this->findModel($upc_product_type_id, $upc_user_id, $upc_year, $upc_month),
        ]);
    }

    /**
     * Creates a new KpiUserProductCommission model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new KpiUserProductCommission();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'upc_product_type_id' => $model->upc_product_type_id, 'upc_user_id' => $model->upc_user_id, 'upc_year' => $model->upc_year, 'upc_month' => $model->upc_month]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing KpiUserProductCommission model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $upc_product_type_id
     * @param integer $upc_user_id
     * @param integer $upc_year
     * @param integer $upc_month
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($upc_product_type_id, $upc_user_id, $upc_year, $upc_month)
    {
        $model = $this->findModel($upc_product_type_id, $upc_user_id, $upc_year, $upc_month);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'upc_product_type_id' => $model->upc_product_type_id, 'upc_user_id' => $model->upc_user_id, 'upc_year' => $model->upc_year, 'upc_month' => $model->upc_month]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing KpiUserProductCommission model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $upc_product_type_id
     * @param integer $upc_user_id
     * @param integer $upc_year
     * @param integer $upc_month
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($upc_product_type_id, $upc_user_id, $upc_year, $upc_month)
    {
        $this->findModel($upc_product_type_id, $upc_user_id, $upc_year, $upc_month)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the KpiUserProductCommission model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $upc_product_type_id
     * @param integer $upc_user_id
     * @param integer $upc_year
     * @param integer $upc_month
     * @return KpiUserProductCommission the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($upc_product_type_id, $upc_user_id, $upc_year, $upc_month)
    {
        if (($model = KpiUserProductCommission::findOne(['upc_product_type_id' => $upc_product_type_id, 'upc_user_id' => $upc_user_id, 'upc_year' => $upc_year, 'upc_month' => $upc_month])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
