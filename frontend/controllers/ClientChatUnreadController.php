<?php

namespace frontend\controllers;

use src\auth\Auth;
use Yii;
use src\model\clientChatUnread\entity\ClientChatUnread;
use src\model\clientChatUnread\entity\search\ClientChatUnreadSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class ClientChatUnreadController extends FController
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
        $searchModel = new ClientChatUnreadSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $id
     *
     * @return string
     *
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
        $model = new ClientChatUnread();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ccu_cc_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     *
     * @return string|Response
     *
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ccu_cc_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     *
     * @return Response
     *
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
     *
     * @return ClientChatUnread
     *
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ClientChatUnread
    {
        if (($model = ClientChatUnread::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
