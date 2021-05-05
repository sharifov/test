<?php

namespace modules\product\controllers;

use frontend\controllers\FController;
use Yii;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use modules\product\src\entities\productQuoteRelation\search\ProductQuoteRelationSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

class ProductQuoteRelationCrudController extends FController
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
        $searchModel = new ProductQuoteRelationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $pqr_parent_pq_id
     * @param integer $pqr_related_pq_id
     * @param integer $pqr_type_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($pqr_parent_pq_id, $pqr_related_pq_id, $pqr_type_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($pqr_parent_pq_id, $pqr_related_pq_id, $pqr_type_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ProductQuoteRelation();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'pqr_parent_pq_id' => $model->pqr_parent_pq_id, 'pqr_related_pq_id' => $model->pqr_related_pq_id, 'pqr_type_id' => $model->pqr_type_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $pqr_parent_pq_id
     * @param integer $pqr_related_pq_id
     * @param integer $pqr_type_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($pqr_parent_pq_id, $pqr_related_pq_id, $pqr_type_id)
    {
        $model = $this->findModel($pqr_parent_pq_id, $pqr_related_pq_id, $pqr_type_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'pqr_parent_pq_id' => $model->pqr_parent_pq_id, 'pqr_related_pq_id' => $model->pqr_related_pq_id, 'pqr_type_id' => $model->pqr_type_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $pqr_parent_pq_id
     * @param integer $pqr_related_pq_id
     * @param integer $pqr_type_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($pqr_parent_pq_id, $pqr_related_pq_id, $pqr_type_id): Response
    {
        $this->findModel($pqr_parent_pq_id, $pqr_related_pq_id, $pqr_type_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $pqr_parent_pq_id
     * @param integer $pqr_related_pq_id
     * @param integer $pqr_type_id
     * @return ProductQuoteRelation
     * @throws NotFoundHttpException
     */
    protected function findModel($pqr_parent_pq_id, $pqr_related_pq_id, $pqr_type_id): ProductQuoteRelation
    {
        if (($model = ProductQuoteRelation::findOne(['pqr_parent_pq_id' => $pqr_parent_pq_id, 'pqr_related_pq_id' => $pqr_related_pq_id, 'pqr_type_id' => $pqr_type_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
