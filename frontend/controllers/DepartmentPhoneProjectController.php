<?php

namespace frontend\controllers;

use Yii;
use common\models\DepartmentPhoneProject;
use common\models\search\DepartmentPhoneProjectSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DepartmentPhoneProjectController implements the CRUD actions for DepartmentPhoneProject model.
 */
class DepartmentPhoneProjectController extends FController
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
     * Lists all DepartmentPhoneProject models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DepartmentPhoneProjectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DepartmentPhoneProject model.
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
     * Creates a new DepartmentPhoneProject model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DepartmentPhoneProject();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->dpp_id]);
        } else {
            $model->dpp_params = '{
  "ivr": {
    "voice_gather_callback_url": "/v1/twilio/voice-gather/",
    "voice_gather_callback_url_v2": "/v2/twilio/voice-gather/",
    "communication_voiceStatusCallbackUrl": "twilio/voice-status-callback",
    "communication_recordingStatusCallbackUrl": "twilio/recording-status-callback",
    "entry_phrase": "Hello, and thank you for calling {{project}}.",
    "entry_voice": "Polly.Joanna",
    "entry_language": "en-US",
    "entry_pause": 2,
    "error_phrase": "No options selected",
    "hold_play": "https://talkdeskapp.s3.amazonaws.com/production/audio_messages/folk_hold_music.mp3",
    "steps": {
      "1": {
        "language": "en-US",
        "voice": "Polly.Joanna",
        "before_say": "",
        "after_say": "",
        "choice": {
          "1": {
            "say": "To speak with our sales representative, press 1.",
            "pause": 1
          },
          "2": {
            "say": "To reach a Customer Exchange agent, press 2.",
            "pause": 1
          },
          "3": {
            "say": "To reach a Customer Support agent, press 3.",
            "pause": 1
          }
        }
      },
      "2": {
        "language": "en-US",
        "voice": "Polly.Joanna",
        "before_say": "Your call is very important to us.",
        "after_say": "This call will be recorded for quality assurance.",
        "digits": {
          "1": {
            "language": "en-US",
            "voice": "Polly.Joanna",
            "pause": 1,
            "say": "Please hold, while you are connected to the next available agent.",
            "play": "https://talkdeskapp.s3.amazonaws.com/production/audio_messages/folk_hold_music.mp3"
          },
          "2": {
            "language": "en-US",
            "voice": "Polly.Joanna",
            "pause": 1,
            "say": "Please hold, while you are connected to the next available agent.",
            "play": "https://talkdeskapp.s3.amazonaws.com/production/audio_messages/folk_hold_music.mp3"
          },
          "3": {
            "language": "en-US",
            "voice": "Polly.Joanna",
            "pause": 1,
            "say": "Please hold, while you are connected to the next available agent.",
            "play": "https://talkdeskapp.s3.amazonaws.com/production/audio_messages/folk_hold_music.mp3"
          }
        }
      }
    }
  }
}';
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DepartmentPhoneProject model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->dpp_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DepartmentPhoneProject model.
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

    /**
     * Finds the DepartmentPhoneProject model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DepartmentPhoneProject the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DepartmentPhoneProject::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
