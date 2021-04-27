<?php

namespace modules\product\controllers;

use Yii;
use modules\product\src\entities\productQuoteOrigin\ProductQuoteOrigin;
use modules\product\src\entities\productQuoteOrigin\search\ProductQuoteOriginSearch;
use frontend\controllers\FController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class ProductQuoteOriginController extends FController
{
    /**
    * @return array
    */
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new ProductQuoteOriginSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $pqo_product_id
     * @param integer $pqo_quote_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($pqo_product_id, $pqo_quote_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($pqo_product_id, $pqo_quote_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ProductQuoteOrigin();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'pqo_product_id' => $model->pqo_product_id, 'pqo_quote_id' => $model->pqo_quote_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $pqo_product_id
     * @param integer $pqo_quote_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($pqo_product_id, $pqo_quote_id)
    {
        $model = $this->findModel($pqo_product_id, $pqo_quote_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'pqo_product_id' => $model->pqo_product_id, 'pqo_quote_id' => $model->pqo_quote_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $pqo_product_id
     * @param integer $pqo_quote_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($pqo_product_id, $pqo_quote_id): Response
    {
        $this->findModel($pqo_product_id, $pqo_quote_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $pqo_product_id
     * @param integer $pqo_quote_id
     * @return ProductQuoteOrigin
     * @throws NotFoundHttpException
     */
    protected function findModel($pqo_product_id, $pqo_quote_id): ProductQuoteOrigin
    {
        if (($model = ProductQuoteOrigin::findOne(['pqo_product_id' => $pqo_product_id, 'pqo_quote_id' => $pqo_quote_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
