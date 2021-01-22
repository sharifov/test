<?php

namespace modules\fileStorage\controllers;

use frontend\controllers\FController;
use Yii;
use modules\fileStorage\src\entity\fileClient\FileClient;
use modules\fileStorage\src\entity\fileClient\search\FileClientSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class FileClientController extends FController
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

    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new FileClientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $fcl_fs_id
     * @param integer $fcl_client_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($fcl_fs_id, $fcl_client_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($fcl_fs_id, $fcl_client_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new FileClient();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'fcl_fs_id' => $model->fcl_fs_id, 'fcl_client_id' => $model->fcl_client_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $fcl_fs_id
     * @param integer $fcl_client_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($fcl_fs_id, $fcl_client_id)
    {
        $model = $this->findModel($fcl_fs_id, $fcl_client_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'fcl_fs_id' => $model->fcl_fs_id, 'fcl_client_id' => $model->fcl_client_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $fcl_fs_id
     * @param integer $fcl_client_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($fcl_fs_id, $fcl_client_id): Response
    {
        $this->findModel($fcl_fs_id, $fcl_client_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $fcl_fs_id
     * @param integer $fcl_client_id
     * @return FileClient
     * @throws NotFoundHttpException
     */
    protected function findModel($fcl_fs_id, $fcl_client_id): FileClient
    {
        if (($model = FileClient::findOne(['fcl_fs_id' => $fcl_fs_id, 'fcl_client_id' => $fcl_client_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
