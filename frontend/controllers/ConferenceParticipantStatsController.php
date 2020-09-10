<?php

namespace frontend\controllers;

use sales\auth\Auth;
use Yii;
use sales\model\conference\entity\conferenceParticipantStats\ConferenceParticipantStats;
use sales\model\conference\entity\conferenceParticipantStats\search\ConferenceParticipantStatsSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class ConferenceParticipantStatsController extends FController
{
    /**
    * @return array
    */
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
        $searchModel = new ConferenceParticipantStatsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ConferenceParticipantStats();

        try {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->cps_id]);
            }
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), 'ConferenceParticipantStats:CRUD:save');
            Yii::$app->session->addFlash('error', $e->getMessage());
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        try {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->cps_id]);
            }
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), 'ConferenceParticipantStats:CRUD:update');
            Yii::$app->session->addFlash('error', $e->getMessage());
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $id
     * @return ConferenceParticipantStats
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ConferenceParticipantStats
    {
        if (($model = ConferenceParticipantStats::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
