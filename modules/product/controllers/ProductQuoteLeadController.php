<?php

namespace modules\product\controllers;

use Yii;
use modules\product\src\entities\productQuoteLead\ProductQuoteLead;
use modules\product\src\entities\productQuoteLead\search\ProductQuoteLeadSearch;
use frontend\controllers\FController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class ProductQuoteLeadController extends FController
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
        $searchModel = new ProductQuoteLeadSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $pql_product_quote_id
     * @param integer $pql_lead_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($pql_product_quote_id, $pql_lead_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($pql_product_quote_id, $pql_lead_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ProductQuoteLead();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'pql_product_quote_id' => $model->pql_product_quote_id, 'pql_lead_id' => $model->pql_lead_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $pql_product_quote_id
     * @param integer $pql_lead_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($pql_product_quote_id, $pql_lead_id)
    {
        $model = $this->findModel($pql_product_quote_id, $pql_lead_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'pql_product_quote_id' => $model->pql_product_quote_id, 'pql_lead_id' => $model->pql_lead_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $pql_product_quote_id
     * @param integer $pql_lead_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($pql_product_quote_id, $pql_lead_id): Response
    {
        $this->findModel($pql_product_quote_id, $pql_lead_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $pql_product_quote_id
     * @param integer $pql_lead_id
     * @return ProductQuoteLead
     * @throws NotFoundHttpException
     */
    protected function findModel($pql_product_quote_id, $pql_lead_id): ProductQuoteLead
    {
        if (($model = ProductQuoteLead::findOne(['pql_product_quote_id' => $pql_product_quote_id, 'pql_lead_id' => $pql_lead_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
