<?php

namespace frontend\controllers;

use common\models\Call;
use src\auth\Auth;
use src\helpers\setting\SettingHelper;
use src\model\callLog\entity\callLog\CallLogQuery;
use src\model\callRecordingLog\entity\CallRecordingLog;
use src\model\conference\entity\conferenceRecordingLog\ConferenceRecordingLog;
use src\repositories\NotFoundException;
use Yii;
use common\models\Conference;
use common\models\search\ConferenceSearch;
use frontend\controllers\FController;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ConferenceController implements the CRUD actions for Conference model.
 */
class ConferenceController extends FController
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
     * Lists all Conference models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ConferenceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Conference model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Conference model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Conference();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->cf_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Conference model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->cf_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Conference model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionRecord(string $conferenceSid)
    {
        $cacheKey = 'conference-recording-url-' . $conferenceSid . '-user-' . Auth::id();

        try {
            if (!$conferenceRecordSid = Yii::$app->cacheFile->get($cacheKey)) {
                if ($conference = Conference::find()->selectRecordingData()->bySid($conferenceSid)->asArray()->one()) {
                    $conferenceRecordSid = $conference['cf_recording_sid'];
                    $conferenceRecordDuration = $conference['cf_recording_duration'] + SettingHelper::getCallRecordingLogAdditionalCacheTimeout();
                } else {
                    throw new NotFoundException('Conference not found');
                }

                Yii::$app->cacheFile->set($cacheKey, $conferenceRecordSid, $conferenceRecordDuration);

                if (SettingHelper::isCallRecordingLogEnabled()) {
                    $conferenceRecordingLog = ConferenceRecordingLog::create($conferenceSid, Auth::id(), (int)date('Y'), (int)date('m'));
                    if (!$conferenceRecordingLog->save(true)) {
                        Yii::error('Conference Recording Log saving failed: ' . $conferenceRecordingLog->getErrorSummary(false)[0], 'ConferenceController::actionRecordingLog::conferenceRecordingLog::save');
                    }
                }
            }
            header('X-Accel-Redirect: ' . Yii::$app->communication->xAccelRedirectCommunicationUrl . $conferenceRecordSid);
        } catch (NotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
    }

    /**
     * Finds the Conference model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Conference the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Conference::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
