<?php

namespace modules\order\controllers;

use frontend\controllers\FController;
use sales\auth\Auth;
use Yii;
use modules\order\src\entities\orderProduct\OrderProduct;
use modules\order\src\entities\orderProduct\search\OrderProductCrudSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

class OrderProductCrudController extends FController
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
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new OrderProductCrudSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $orp_order_id
     * @param $orp_product_quote_id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($orp_order_id, $orp_product_quote_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($orp_order_id, $orp_product_quote_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new OrderProduct();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'orp_order_id' => $model->orp_order_id, 'orp_product_quote_id' => $model->orp_product_quote_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param $orp_order_id
     * @param $orp_product_quote_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($orp_order_id, $orp_product_quote_id)
    {
        $model = $this->findModel($orp_order_id, $orp_product_quote_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'orp_order_id' => $model->orp_order_id, 'orp_product_quote_id' => $model->orp_product_quote_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $orp_order_id
     * @param $orp_product_quote_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($orp_order_id, $orp_product_quote_id): Response
    {
        $this->findModel($orp_order_id, $orp_product_quote_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param $orp_order_id
     * @param $orp_product_quote_id
     * @return OrderProduct
     * @throws NotFoundHttpException
     */
    protected function findModel($orp_order_id, $orp_product_quote_id): OrderProduct
    {
        if (($model = OrderProduct::findOne(['orp_order_id' => $orp_order_id, 'orp_product_quote_id' => $orp_product_quote_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
