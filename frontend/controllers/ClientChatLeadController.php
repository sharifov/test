<?php

namespace frontend\controllers;

use sales\auth\Auth;
use sales\model\clientChatLead\entity\ClientChatLead;
use sales\model\clientChatLead\entity\search\ClientChatLeadSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

class ClientChatLeadController extends FController
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

    public function actionIndex(): string
    {
        $searchModel = new ClientChatLeadSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($ccl_chat_id, $ccl_lead_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($ccl_chat_id, $ccl_lead_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ClientChatLead();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ccl_chat_id' => $model->ccl_chat_id, 'ccl_lead_id' => $model->ccl_lead_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($ccl_chat_id, $ccl_lead_id)
    {
        $model = $this->findModel($ccl_chat_id, $ccl_lead_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ccl_chat_id' => $model->ccl_chat_id, 'ccl_lead_id' => $model->ccl_lead_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($ccl_chat_id, $ccl_lead_id): Response
    {
        $this->findModel($ccl_chat_id, $ccl_lead_id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($ccl_chat_id, $ccl_lead_id): ClientChatLead
    {
        if (($model = ClientChatLead::findOne(['ccl_chat_id' => $ccl_chat_id, 'ccl_lead_id' => $ccl_lead_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
