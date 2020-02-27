<?php

namespace modules\order\controllers;

use frontend\controllers\FController;
use Yii;
use modules\order\src\entities\orderUserProfit\OrderUserProfit;
use modules\order\src\entities\orderUserProfit\search\OrderUserProfitSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderUserProfitCrudController implements the CRUD actions for OrderUserProfit model.
 */
class OrderUserProfitCrudController extends FController
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
     * Lists all OrderUserProfit models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderUserProfitSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OrderUserProfit model.
     * @param integer $oup_order_id
     * @param integer $oup_user_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($oup_order_id, $oup_user_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($oup_order_id, $oup_user_id),
        ]);
    }

    /**
     * Creates a new OrderUserProfit model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderUserProfit();
        $model->setScenario(OrderUserProfit::SCENARIO_CRUD);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'oup_order_id' => $model->oup_order_id, 'oup_user_id' => $model->oup_user_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing OrderUserProfit model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $oup_order_id
     * @param integer $oup_user_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($oup_order_id, $oup_user_id)
    {
        $model = $this->findModel($oup_order_id, $oup_user_id);
		$model->setScenario(OrderUserProfit::SCENARIO_CRUD);

        if ($model->load(Yii::$app->request->post()) && $model->save(true)) {
            return $this->redirect(['view', 'oup_order_id' => $model->oup_order_id, 'oup_user_id' => $model->oup_user_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing OrderUserProfit model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $oup_order_id
     * @param integer $oup_user_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($oup_order_id, $oup_user_id)
    {
        $this->findModel($oup_order_id, $oup_user_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the OrderUserProfit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $oup_order_id
     * @param integer $oup_user_id
     * @return OrderUserProfit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($oup_order_id, $oup_user_id)
    {
        if (($model = OrderUserProfit::findOne(['oup_order_id' => $oup_order_id, 'oup_user_id' => $oup_user_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
