<?php

namespace frontend\controllers;

use Yii;
use sales\model\leadOrder\entity\LeadOrder;
use sales\model\leadOrder\entity\search\LeadOrderSearch;
use frontend\controllers\FController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

class LeadOrderCrudController extends FController
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
        $searchModel = new LeadOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $lo_order_id
     * @param integer $lo_lead_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($lo_order_id, $lo_lead_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($lo_order_id, $lo_lead_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new LeadOrder();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'lo_order_id' => $model->lo_order_id, 'lo_lead_id' => $model->lo_lead_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $lo_order_id
     * @param integer $lo_lead_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($lo_order_id, $lo_lead_id)
    {
        $model = $this->findModel($lo_order_id, $lo_lead_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'lo_order_id' => $model->lo_order_id, 'lo_lead_id' => $model->lo_lead_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $lo_order_id
     * @param integer $lo_lead_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($lo_order_id, $lo_lead_id): Response
    {
        $this->findModel($lo_order_id, $lo_lead_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $lo_order_id
     * @param integer $lo_lead_id
     * @return LeadOrder
     * @throws NotFoundHttpException
     */
    protected function findModel($lo_order_id, $lo_lead_id): LeadOrder
    {
        if (($model = LeadOrder::findOne(['lo_order_id' => $lo_order_id, 'lo_lead_id' => $lo_lead_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
