<?php

namespace frontend\controllers;

use Yii;
use sales\model\leadProduct\entity\LeadProduct;
use sales\model\leadProduct\entity\search\LeadProductSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

class LeadProductCrudController extends FController
{
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
        $searchModel = new LeadProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $lp_lead_id
     * @param integer $lp_product_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($lp_lead_id, $lp_product_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($lp_lead_id, $lp_product_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new LeadProduct();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'lp_lead_id' => $model->lp_lead_id, 'lp_product_id' => $model->lp_product_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $lp_lead_id
     * @param integer $lp_product_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($lp_lead_id, $lp_product_id)
    {
        $model = $this->findModel($lp_lead_id, $lp_product_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'lp_lead_id' => $model->lp_lead_id, 'lp_product_id' => $model->lp_product_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $lp_lead_id
     * @param integer $lp_product_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($lp_lead_id, $lp_product_id): Response
    {
        $this->findModel($lp_lead_id, $lp_product_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $lp_lead_id
     * @param integer $lp_product_id
     * @return LeadProduct
     * @throws NotFoundHttpException
     */
    protected function findModel($lp_lead_id, $lp_product_id): LeadProduct
    {
        if (($model = LeadProduct::findOne(['lp_lead_id' => $lp_lead_id, 'lp_product_id' => $lp_product_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
