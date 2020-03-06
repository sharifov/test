<?php

namespace frontend\controllers;

use Yii;
use sales\model\kpi\entity\kpiProductCommission\KpiProductCommission;
use sales\model\kpi\entity\kpiProductCommission\search\KpiProductCommissionSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * KpiProductCommissionCrudController implements the CRUD actions for KpiProductCommission model.
 */
class KpiProductCommissionCrudController extends FController
{
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
     * Lists all KpiProductCommission models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new KpiProductCommissionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single KpiProductCommission model.
     * @param integer $pc_product_type_id
     * @param integer $pc_performance
     * @param integer $pc_commission_percent
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($pc_product_type_id, $pc_performance, $pc_commission_percent)
    {
        return $this->render('view', [
            'model' => $this->findModel($pc_product_type_id, $pc_performance, $pc_commission_percent),
        ]);
    }

    /**
     * Creates a new KpiProductCommission model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new KpiProductCommission();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'pc_product_type_id' => $model->pc_product_type_id, 'pc_performance' => $model->pc_performance, 'pc_commission_percent' => $model->pc_commission_percent]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing KpiProductCommission model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $pc_product_type_id
     * @param integer $pc_performance
     * @param integer $pc_commission_percent
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($pc_product_type_id, $pc_performance, $pc_commission_percent)
    {
        $model = $this->findModel($pc_product_type_id, $pc_performance, $pc_commission_percent);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'pc_product_type_id' => $model->pc_product_type_id, 'pc_performance' => $model->pc_performance, 'pc_commission_percent' => $model->pc_commission_percent]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing KpiProductCommission model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $pc_product_type_id
     * @param integer $pc_performance
     * @param integer $pc_commission_percent
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($pc_product_type_id, $pc_performance, $pc_commission_percent)
    {
        $this->findModel($pc_product_type_id, $pc_performance, $pc_commission_percent)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the KpiProductCommission model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $pc_product_type_id
     * @param integer $pc_performance
     * @param integer $pc_commission_percent
     * @return KpiProductCommission the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($pc_product_type_id, $pc_performance, $pc_commission_percent)
    {
        if (($model = KpiProductCommission::findOne(['pc_product_type_id' => $pc_product_type_id, 'pc_performance' => $pc_performance, 'pc_commission_percent' => $pc_commission_percent])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
