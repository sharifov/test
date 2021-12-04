<?php

namespace frontend\controllers;

use Yii;
use sales\model\voip\phoneDevice\PhoneDeviceLog;
use sales\model\voip\phoneDevice\PhoneDeviceLogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class PhoneDeviceLogController extends Controller
{
    /**
    * @return array
    */
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new PhoneDeviceLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $pdl_id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($pdl_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($pdl_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new PhoneDeviceLog();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'pdl_id' => $model->pdl_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $pdl_id ID
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($pdl_id)
    {
        $model = $this->findModel($pdl_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'pdl_id' => $model->pdl_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $pdl_id ID
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($pdl_id): Response
    {
        $this->findModel($pdl_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param int $id ID
     * @return PhoneDeviceLog
     * @throws NotFoundHttpException
     */
    protected function findModel($id): PhoneDeviceLog
    {
        if (($model = PhoneDeviceLog::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
