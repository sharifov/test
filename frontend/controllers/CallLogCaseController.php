<?php

namespace frontend\controllers;

use Yii;
use sales\model\callLog\entity\callLogCase\CallLogCase;
use sales\model\callLog\entity\callLogCase\search\CallLogCaseSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

class CallLogCaseController extends FController
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
        $searchModel = new CallLogCaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $clc_cl_id
     * @param $clc_case_id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($clc_cl_id, $clc_case_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($clc_cl_id, $clc_case_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new CallLogCase();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'clc_cl_id' => $model->clc_cl_id, 'clc_case_id' => $model->clc_case_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param $clc_cl_id
     * @param $clc_case_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($clc_cl_id, $clc_case_id)
    {
        $model = $this->findModel($clc_cl_id, $clc_case_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'clc_cl_id' => $model->clc_cl_id, 'clc_case_id' => $model->clc_case_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $clc_cl_id
     * @param $clc_case_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($clc_cl_id, $clc_case_id): Response
    {
        $this->findModel($clc_cl_id, $clc_case_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param $clc_cl_id
     * @param $clc_case_id
     * @return CallLogCase
     * @throws NotFoundHttpException
     */
    protected function findModel($clc_cl_id, $clc_case_id): CallLogCase
    {
        if (($model = CallLogCase::findOne(['clc_cl_id' => $clc_cl_id, 'clc_case_id' => $clc_case_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
