<?php

namespace frontend\controllers;

use Yii;
use sales\model\callLog\entity\callLogLead\CallLogLead;
use sales\model\callLog\entity\callLogLead\search\CallLogLeadSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

class CallLogLeadController extends FController
{
    public function behaviors()
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
        $searchModel = new CallLogLeadSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $cll_cl_id
     * @param $cll_lead_id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($cll_cl_id, $cll_lead_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($cll_cl_id, $cll_lead_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new CallLogLead();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cll_cl_id' => $model->cll_cl_id, 'cll_lead_id' => $model->cll_lead_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param $cll_cl_id
     * @param $cll_lead_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($cll_cl_id, $cll_lead_id)
    {
        $model = $this->findModel($cll_cl_id, $cll_lead_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cll_cl_id' => $model->cll_cl_id, 'cll_lead_id' => $model->cll_lead_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $cll_cl_id
     * @param $cll_lead_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($cll_cl_id, $cll_lead_id): Response
    {
        $this->findModel($cll_cl_id, $cll_lead_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param $cll_cl_id
     * @param $cll_lead_id
     * @return CallLogLead
     * @throws NotFoundHttpException
     */
    protected function findModel($cll_cl_id, $cll_lead_id): CallLogLead
    {
        if (($model = CallLogLead::findOne(['cll_cl_id' => $cll_cl_id, 'cll_lead_id' => $cll_lead_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
