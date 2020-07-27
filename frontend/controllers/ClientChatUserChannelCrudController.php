<?php

namespace frontend\controllers;

use Yii;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use sales\model\clientChatUserChannel\entity\search\ClientChatUserChannelSearch;
use frontend\controllers\FController;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class ClientChatUserChannelCrudController extends FController
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
        $searchModel = new ClientChatUserChannelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $ccuc_user_id
     * @param integer $ccuc_channel_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ccuc_user_id, $ccuc_channel_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($ccuc_user_id, $ccuc_channel_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ClientChatUserChannel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ccuc_user_id' => $model->ccuc_user_id, 'ccuc_channel_id' => $model->ccuc_channel_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $ccuc_user_id
     * @param integer $ccuc_channel_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($ccuc_user_id, $ccuc_channel_id)
    {
        $model = $this->findModel($ccuc_user_id, $ccuc_channel_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ccuc_user_id' => $model->ccuc_user_id, 'ccuc_channel_id' => $model->ccuc_channel_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $ccuc_user_id
     * @param integer $ccuc_channel_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($ccuc_user_id, $ccuc_channel_id): Response
    {
        $this->findModel($ccuc_user_id, $ccuc_channel_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $ccuc_user_id
     * @param integer $ccuc_channel_id
     * @return ClientChatUserChannel
     * @throws NotFoundHttpException
     */
    protected function findModel($ccuc_user_id, $ccuc_channel_id): ClientChatUserChannel
    {
        if (($model = ClientChatUserChannel::findOne(['ccuc_user_id' => $ccuc_user_id, 'ccuc_channel_id' => $ccuc_channel_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
