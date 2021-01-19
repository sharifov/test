<?php

namespace frontend\controllers;

use Yii;
use sales\model\callRecordingLog\entity\CallRecordingLog;
use sales\model\callRecordingLog\entity\search\CallRecordingLogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

class CallRecordingLogCrudController extends FController
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
        $searchModel = new CallRecordingLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $crl_id
     * @param integer $crl_year
     * @param integer $crl_month
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($crl_id, $crl_year, $crl_month): string
    {
        return $this->render('view', [
            'model' => $this->findModel($crl_id, $crl_year, $crl_month),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new CallRecordingLog();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'crl_id' => $model->crl_id, 'crl_year' => $model->crl_year, 'crl_month' => $model->crl_month]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $crl_id
     * @param integer $crl_year
     * @param integer $crl_month
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($crl_id, $crl_year, $crl_month)
    {
        $model = $this->findModel($crl_id, $crl_year, $crl_month);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'crl_id' => $model->crl_id, 'crl_year' => $model->crl_year, 'crl_month' => $model->crl_month]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $crl_id
     * @param integer $crl_year
     * @param integer $crl_month
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($crl_id, $crl_year, $crl_month): Response
    {
        $this->findModel($crl_id, $crl_year, $crl_month)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $crl_id
     * @param integer $crl_year
     * @param integer $crl_month
     * @return CallRecordingLog
     * @throws NotFoundHttpException
     */
    protected function findModel($crl_id, $crl_year, $crl_month): CallRecordingLog
    {
        if (($model = CallRecordingLog::findOne(['crl_id' => $crl_id, 'crl_year' => $crl_year, 'crl_month' => $crl_month])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
