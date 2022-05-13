<?php

namespace frontend\controllers;

use common\models\ClientChatSurveyResponse;
use common\models\search\ClientChatSurveyResponseSearch;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * Class ClientChatSurveyResponseController
 * @package frontend\controllers
 */
class ClientChatSurveyResponseController extends FController
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * @param $ccsr_id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($ccsr_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($ccsr_id)
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionCreate($ccs_id)
    {
        $model = new ClientChatSurveyResponse();
        $model->ccsr_client_chat_survey_id = $ccs_id;

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['client-chat-survey/view', 'ccs_id' => $ccs_id]);
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param $ccsr_id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($ccsr_id)
    {
        $model = $this->findModel($ccsr_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['client-chat-survey/view', 'ccs_id' => $model->ccsr_client_chat_survey_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $ccsr_id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($ccsr_id)
    {
        $model = $this->findModel($ccsr_id);
        $model->delete();

        return $this->redirect(['client-chat-survey/view', 'ccs_id' => $model->ccsr_client_chat_survey_id]);
    }

    /**
     * @param $ccsr_id
     * @return ClientChatSurveyResponse|null
     * @throws NotFoundHttpException
     */
    protected function findModel($ccsr_id)
    {
        if (($model = ClientChatSurveyResponse::findOne(['ccsr_id' => $ccsr_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
