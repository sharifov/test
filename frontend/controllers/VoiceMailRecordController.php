<?php

namespace frontend\controllers;

use common\models\Notifications;
use src\auth\Auth;
use Yii;
use src\model\voiceMailRecord\entity\VoiceMailRecord;
use src\model\voiceMailRecord\entity\search\VoiceMailRecordSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class VoiceMailRecordController extends FController
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

    public function beforeAction($action): bool
    {
        if ($action->id === 'count') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new VoiceMailRecordSearch();
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
        $model = new VoiceMailRecord();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->vmr_call_id]);
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->vmr_call_id]);
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
     * @return VoiceMailRecord
     * @throws NotFoundHttpException
     */
    protected function findModel($id): VoiceMailRecord
    {
        if (($model = VoiceMailRecord::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param integer $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionRemove($id): Response
    {
        $model = $this->findModel($id);
        $model->vmr_deleted = true;
        if (!$model->save()) {
            Yii::$app->session->addFlash('error', 'Server error. Try again later.');
        }

        return $this->redirect(['list']);
    }

    /**
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionShowed(): Response
    {
        $callId = (int)Yii::$app->request->post('callId');
        $model = $this->findModel($callId);
        if (!$model->isOwner((int)Auth::id())) {
            return $this->asJson([
                'error' => true,
                'message' => 'Is not your voice mail'
            ]);
        }
        $model->vmr_new = false;
        if (!$model->save()) {
            return $this->asJson([
                'error' => true,
                'message' => 'Saved error'
            ]);
        }
        Notifications::publish('updateVoiceMailRecord', ['user_id' => $model->vmr_user_id], []);
        return $this->asJson([
            'error' => false,
            'message' => 'OK'
        ]);
    }

    /**
     * @return string
     */
    public function actionList(): string
    {
        $searchModel = new VoiceMailRecordSearch();
        $dataProvider = $searchModel->list(Yii::$app->request->queryParams, Auth::user());

        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCount(): Response
    {
        return $this->asJson([
            'count' => VoiceMailRecord::find()->andWhere(['vmr_user_id' => Auth::id(), 'vmr_new' => true, 'vmr_deleted' => false])->count()
        ]);
    }

    public function actionRecord(string $callId)
    {
        $record = VoiceMailRecord::find()->andWhere(['vmr_call_id' => $callId])->andWhere(['IS NOT', 'vmr_record_sid', null])->asArray()->one();
        if (!$record) {
            throw new NotFoundHttpException();
        }
        //todo validate permissions
        header('X-Accel-Redirect: ' . Yii::$app->comms->xAccelRedirectCommsUrl . $record['vmr_record_sid']);
    }
}
