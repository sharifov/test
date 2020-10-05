<?php

namespace frontend\controllers;

use Yii;
use sales\model\user\entity\userConnectionActiveChat\UserConnectionActiveChat;
use sales\model\user\entity\userConnectionActiveChat\search\UserConnectionActiveChatSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class UserConnectionActiveChatController extends FController
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
        $searchModel = new UserConnectionActiveChatSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $ucac_conn_id
     * @param integer $ucac_chat_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ucac_conn_id, $ucac_chat_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($ucac_conn_id, $ucac_chat_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new UserConnectionActiveChat();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ucac_conn_id' => $model->ucac_conn_id, 'ucac_chat_id' => $model->ucac_chat_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $ucac_conn_id
     * @param integer $ucac_chat_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($ucac_conn_id, $ucac_chat_id)
    {
        $model = $this->findModel($ucac_conn_id, $ucac_chat_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ucac_conn_id' => $model->ucac_conn_id, 'ucac_chat_id' => $model->ucac_chat_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $ucac_conn_id
     * @param integer $ucac_chat_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($ucac_conn_id, $ucac_chat_id): Response
    {
        $this->findModel($ucac_conn_id, $ucac_chat_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $ucac_conn_id
     * @param integer $ucac_chat_id
     * @return UserConnectionActiveChat
     * @throws NotFoundHttpException
     */
    protected function findModel($ucac_conn_id, $ucac_chat_id): UserConnectionActiveChat
    {
        if (($model = UserConnectionActiveChat::findOne(['ucac_conn_id' => $ucac_conn_id, 'ucac_chat_id' => $ucac_chat_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
