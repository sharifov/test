<?php

namespace frontend\controllers;

use Yii;
use sales\model\caseOrder\entity\CaseOrder;
use sales\model\caseOrder\entity\search\CaseOrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

class CaseOrderCrudController extends FController
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
        $searchModel = new CaseOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $co_order_id
     * @param integer $co_case_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($co_order_id, $co_case_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($co_order_id, $co_case_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new CaseOrder();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'co_order_id' => $model->co_order_id, 'co_case_id' => $model->co_case_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $co_order_id
     * @param integer $co_case_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($co_order_id, $co_case_id)
    {
        $model = $this->findModel($co_order_id, $co_case_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'co_order_id' => $model->co_order_id, 'co_case_id' => $model->co_case_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $co_order_id
     * @param integer $co_case_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($co_order_id, $co_case_id): Response
    {
        $this->findModel($co_order_id, $co_case_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $co_order_id
     * @param integer $co_case_id
     * @return CaseOrder
     * @throws NotFoundHttpException
     */
    protected function findModel($co_order_id, $co_case_id): CaseOrder
    {
        if (($model = CaseOrder::findOne(['co_order_id' => $co_order_id, 'co_case_id' => $co_case_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
