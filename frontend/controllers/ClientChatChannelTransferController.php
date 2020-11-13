<?php

namespace frontend\controllers;

use sales\auth\Auth;
use Yii;
use sales\model\clientChatChannelTransfer\entity\ClientChatChannelTransfer;
use sales\model\clientChatChannelTransfer\entity\search\ClientChatChannelTransferSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class ClientChatChannelTransferController extends FController
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
        $searchModel = new ClientChatChannelTransferSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $cctr_from_ccc_id
     * @param integer $cctr_to_ccc_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($cctr_from_ccc_id, $cctr_to_ccc_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($cctr_from_ccc_id, $cctr_to_ccc_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ClientChatChannelTransfer();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cctr_from_ccc_id' => $model->cctr_from_ccc_id, 'cctr_to_ccc_id' => $model->cctr_to_ccc_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $cctr_from_ccc_id
     * @param integer $cctr_to_ccc_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($cctr_from_ccc_id, $cctr_to_ccc_id)
    {
        $model = $this->findModel($cctr_from_ccc_id, $cctr_to_ccc_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cctr_from_ccc_id' => $model->cctr_from_ccc_id, 'cctr_to_ccc_id' => $model->cctr_to_ccc_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $cctr_from_ccc_id
     * @param integer $cctr_to_ccc_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($cctr_from_ccc_id, $cctr_to_ccc_id): Response
    {
        $this->findModel($cctr_from_ccc_id, $cctr_to_ccc_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $cctr_from_ccc_id
     * @param integer $cctr_to_ccc_id
     * @return ClientChatChannelTransfer
     * @throws NotFoundHttpException
     */
    protected function findModel($cctr_from_ccc_id, $cctr_to_ccc_id): ClientChatChannelTransfer
    {
        if (($model = ClientChatChannelTransfer::findOne(['cctr_from_ccc_id' => $cctr_from_ccc_id, 'cctr_to_ccc_id' => $cctr_to_ccc_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
