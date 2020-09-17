<?php

namespace frontend\controllers;

use Yii;
use sales\model\clientChat\entity\channelTranslate\ClientChatChannelTranslate;
use sales\model\clientChat\entity\channelTranslate\search\ClientChatChannelTranslateSearch;
use frontend\controllers\FController;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ClientChatChannelTranslateController implements the CRUD actions for ClientChatChannelTranslate model.
 */
class ClientChatChannelTranslateController extends FController
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
     * Lists all ClientChatChannelTranslate models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClientChatChannelTranslateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ClientChatChannelTranslate model.
     * @param integer $ct_channel_id
     * @param string $ct_language_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ct_channel_id, $ct_language_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($ct_channel_id, $ct_language_id),
        ]);
    }

    /**
     * Creates a new ClientChatChannelTranslate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ClientChatChannelTranslate();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ct_channel_id' => $model->ct_channel_id, 'ct_language_id' => $model->ct_language_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ClientChatChannelTranslate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $ct_channel_id
     * @param string $ct_language_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($ct_channel_id, $ct_language_id)
    {
        $model = $this->findModel($ct_channel_id, $ct_language_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ct_channel_id' => $model->ct_channel_id, 'ct_language_id' => $model->ct_language_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ClientChatChannelTranslate model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $ct_channel_id
     * @param string $ct_language_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($ct_channel_id, $ct_language_id)
    {
        $this->findModel($ct_channel_id, $ct_language_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ClientChatChannelTranslate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $ct_channel_id
     * @param string $ct_language_id
     * @return ClientChatChannelTranslate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ct_channel_id, $ct_language_id)
    {
        if (($model = ClientChatChannelTranslate::findOne(['ct_channel_id' => $ct_channel_id, 'ct_language_id' => $ct_language_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
