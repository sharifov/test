<?php

namespace frontend\controllers;

use Yii;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\model\clientChatUserAccess\entity\search\ClientChatUserAccessSearch;
use frontend\controllers\FController;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class ClientChatUserAccessCrudController extends FController
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
        $searchModel = new ClientChatUserAccessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $ccua_cch_id
     * @param integer $ccua_user_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ccua_cch_id, $ccua_user_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($ccua_cch_id, $ccua_user_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ClientChatUserAccess();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ccua_cch_id' => $model->ccua_cch_id, 'ccua_user_id' => $model->ccua_user_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $ccua_cch_id
     * @param integer $ccua_user_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($ccua_cch_id, $ccua_user_id)
    {
        $model = $this->findModel($ccua_cch_id, $ccua_user_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ccua_cch_id' => $model->ccua_cch_id, 'ccua_user_id' => $model->ccua_user_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $ccua_cch_id
     * @param integer $ccua_user_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($ccua_cch_id, $ccua_user_id): Response
    {
        $this->findModel($ccua_cch_id, $ccua_user_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $ccua_cch_id
     * @param integer $ccua_user_id
     * @return ClientChatUserAccess
     * @throws NotFoundHttpException
     */
    protected function findModel($ccua_cch_id, $ccua_user_id): ClientChatUserAccess
    {
        if (($model = ClientChatUserAccess::findOne(['ccua_cch_id' => $ccua_cch_id, 'ccua_user_id' => $ccua_user_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
