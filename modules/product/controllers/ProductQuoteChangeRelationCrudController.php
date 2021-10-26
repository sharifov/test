<?php

namespace modules\product\controllers;

use frontend\controllers\FController;
use Yii;
use modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelation;
use modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

/**
 * Class ProductQuoteChangeRelationCrudController
 */
class ProductQuoteChangeRelationCrudController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

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
        $searchModel = new ProductQuoteChangeRelationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $pqcr_pqc_id
     * @param integer $pqcr_pq_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($pqcr_pqc_id, $pqcr_pq_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($pqcr_pqc_id, $pqcr_pq_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ProductQuoteChangeRelation();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'pqcr_pqc_id' => $model->pqcr_pqc_id, 'pqcr_pq_id' => $model->pqcr_pq_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $pqcr_pqc_id
     * @param integer $pqcr_pq_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($pqcr_pqc_id, $pqcr_pq_id)
    {
        $model = $this->findModel($pqcr_pqc_id, $pqcr_pq_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'pqcr_pqc_id' => $model->pqcr_pqc_id, 'pqcr_pq_id' => $model->pqcr_pq_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $pqcr_pqc_id
     * @param integer $pqcr_pq_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($pqcr_pqc_id, $pqcr_pq_id): Response
    {
        $this->findModel($pqcr_pqc_id, $pqcr_pq_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $pqcr_pqc_id
     * @param integer $pqcr_pq_id
     * @return ProductQuoteChangeRelation
     * @throws NotFoundHttpException
     */
    protected function findModel($pqcr_pqc_id, $pqcr_pq_id): ProductQuoteChangeRelation
    {
        if (($model = ProductQuoteChangeRelation::findOne(['pqcr_pqc_id' => $pqcr_pqc_id, 'pqcr_pq_id' => $pqcr_pq_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
