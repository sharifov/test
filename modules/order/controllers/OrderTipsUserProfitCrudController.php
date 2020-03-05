<?php

namespace modules\order\controllers;

use frontend\controllers\FController;
use modules\order\src\entities\orderUserProfit\OrderUserProfit;
use Yii;
use modules\order\src\entities\orderTipsUserProfit\OrderTipsUserProfit;
use modules\order\src\entities\orderTipsUserProfit\search\OrderTipsUserProfitSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderTipsUserProfitCrudController implements the CRUD actions for OrderTipsUserProfit model.
 */
class OrderTipsUserProfitCrudController extends FController
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
					'delete-ajax' => ['POST'],
				],
			],
		];
		return ArrayHelper::merge(parent::behaviors(), $behaviors);
	}

    /**
     * Lists all OrderTipsUserProfit models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderTipsUserProfitSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OrderTipsUserProfit model.
     * @param integer $otup_order_id
     * @param integer $otup_user_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($otup_order_id, $otup_user_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($otup_order_id, $otup_user_id),
        ]);
    }

    /**
     * Creates a new OrderTipsUserProfit model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderTipsUserProfit();
		$model->setScenario(OrderUserProfit::SCENARIO_CRUD);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'otup_order_id' => $model->otup_order_id, 'otup_user_id' => $model->otup_user_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing OrderTipsUserProfit model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $otup_order_id
     * @param integer $otup_user_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($otup_order_id, $otup_user_id)
    {
        $model = $this->findModel($otup_order_id, $otup_user_id);
		$model->setScenario(OrderUserProfit::SCENARIO_CRUD);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'otup_order_id' => $model->otup_order_id, 'otup_user_id' => $model->otup_user_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing OrderTipsUserProfit model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $otup_order_id
     * @param integer $otup_user_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($otup_order_id, $otup_user_id)
    {
        $this->findModel($otup_order_id, $otup_user_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the OrderTipsUserProfit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $otup_order_id
     * @param integer $otup_user_id
     * @return OrderTipsUserProfit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($otup_order_id, $otup_user_id)
    {
        if (($model = OrderTipsUserProfit::findOne(['otup_order_id' => $otup_order_id, 'otup_user_id' => $otup_user_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
