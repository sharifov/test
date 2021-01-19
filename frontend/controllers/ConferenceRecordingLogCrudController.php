<?php

namespace frontend\controllers;

use Yii;
use sales\model\conference\entity\conferenceRecordingLog\ConferenceRecordingLog;
use sales\model\conference\entity\conferenceRecordingLog\search\ConferenceRecordingLogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

class ConferenceRecordingLogCrudController extends FController
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
        $searchModel = new ConferenceRecordingLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $cfrl_id
     * @param integer $cfrl_year
     * @param integer $cfrl_month
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($cfrl_id, $cfrl_year, $cfrl_month): string
    {
        return $this->render('view', [
            'model' => $this->findModel($cfrl_id, $cfrl_year, $cfrl_month),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ConferenceRecordingLog();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cfrl_id' => $model->cfrl_id, 'cfrl_year' => $model->cfrl_year, 'cfrl_month' => $model->cfrl_month]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $cfrl_id
     * @param integer $cfrl_year
     * @param integer $cfrl_month
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($cfrl_id, $cfrl_year, $cfrl_month)
    {
        $model = $this->findModel($cfrl_id, $cfrl_year, $cfrl_month);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cfrl_id' => $model->cfrl_id, 'cfrl_year' => $model->cfrl_year, 'cfrl_month' => $model->cfrl_month]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $cfrl_id
     * @param integer $cfrl_year
     * @param integer $cfrl_month
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($cfrl_id, $cfrl_year, $cfrl_month): Response
    {
        $this->findModel($cfrl_id, $cfrl_year, $cfrl_month)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $cfrl_id
     * @param integer $cfrl_year
     * @param integer $cfrl_month
     * @return ConferenceRecordingLog
     * @throws NotFoundHttpException
     */
    protected function findModel($cfrl_id, $cfrl_year, $cfrl_month): ConferenceRecordingLog
    {
        if (($model = ConferenceRecordingLog::findOne(['cfrl_id' => $cfrl_id, 'cfrl_year' => $cfrl_year, 'cfrl_month' => $cfrl_month])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
