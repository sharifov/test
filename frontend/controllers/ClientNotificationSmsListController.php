<?php

namespace frontend\controllers;

use src\auth\Auth;
use Yii;
use src\model\client\notifications\sms\entity\ClientNotificationSmsList;
use src\model\client\notifications\sms\entity\search\ClientNotificationSmsListSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class ClientNotificationSmsListController extends FController
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors =  [
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
        $searchModel = new ClientNotificationSmsListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $id
     * @return string
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
        $model = new ClientNotificationSmsList();
        $model->cnsl_created_dt = date('Y-m-d H:i:s');

        if ($model->load(Yii::$app->request->post())) {
            try {
                $model->cnsl_data_json = Json::decode($model->cnsl_data_json);
            } catch (\Throwable $e) {
                Yii::$app->session->addFlash('error', 'DataJson: ' . $e->getMessage());
                $model->cnsl_data_json = [];
            }

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->cnsl_id]);
            }
        }

        if (!$model->cnsl_data_json) {
            $model->cnsl_data_json = [];
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $originalParams = $model->cnsl_data_json;

        $model->cnsl_updated_dt = date('Y-m-d H:i:s');

        if ($model->load(Yii::$app->request->post())) {
            try {
                $model->cnsl_data_json = Json::decode($model->cnsl_data_json);
            } catch (\Throwable $e) {
                Yii::$app->session->addFlash('error', 'DataJson: ' . $e->getMessage());
                $model->cnsl_data_json = $originalParams;
            }

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->cnsl_id]);
            }
        }

        if (!$model->cnsl_data_json) {
            $model->cnsl_data_json = [];
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return Response
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
     * @return ClientNotificationSmsList
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ClientNotificationSmsList
    {
        if (($model = ClientNotificationSmsList::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
