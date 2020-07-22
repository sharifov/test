<?php

namespace frontend\controllers;

use sales\auth\Auth;
use sales\model\clientChatCase\entity\ClientChatCase;
use sales\model\clientChatCase\entity\search\ClientChatCaseSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

class ClientChatCaseController extends FController
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
        $searchModel = new ClientChatCaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($cccs_chat_id, $cccs_case_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($cccs_chat_id, $cccs_case_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ClientChatCase();
        $model->cccs_created_dt = date('Y-m-d H:i:s');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cccs_chat_id' => $model->cccs_chat_id, 'cccs_case_id' => $model->cccs_case_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($cccs_chat_id, $cccs_case_id)
    {
        $model = $this->findModel($cccs_chat_id, $cccs_case_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cccs_chat_id' => $model->cccs_chat_id, 'cccs_case_id' => $model->cccs_case_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($cccs_chat_id, $cccs_case_id): Response
    {
        $this->findModel($cccs_chat_id, $cccs_case_id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($cccs_chat_id, $cccs_case_id): ClientChatCase
    {
        if (($model = ClientChatCase::findOne(['cccs_chat_id' => $cccs_chat_id, 'cccs_case_id' => $cccs_case_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
