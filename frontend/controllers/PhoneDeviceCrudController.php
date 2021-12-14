<?php

namespace frontend\controllers;

use sales\auth\Auth;
use Yii;
use sales\model\voip\phoneDevice\device\PhoneDevice;
use sales\model\voip\phoneDevice\device\PhoneDeviceSearch;
use yii\caching\TagDependency;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

class PhoneDeviceCrudController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'invalidate-cache-token' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function actionInvalidateCacheToken()
    {
        TagDependency::invalidate(\Yii::$app->cache, 'twilio_jwt_token');
        Yii::$app->session->addFlash('twilio_jwt_clean', true);
        return $this->redirect(['index']);
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new PhoneDeviceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

//    /**
//     * @return string|Response
//     */
//    public function actionCreate()
//    {
//        $model = new PhoneDevice();
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'pd_id' => $model->pd_id]);
//        }
//
//        return $this->render('create', [
//            'model' => $model,
//        ]);
//    }

    /**
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->pd_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $id ID
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
     * @param int $id ID
     * @return PhoneDevice
     * @throws NotFoundHttpException
     */
    protected function findModel($id): PhoneDevice
    {
        if (($model = PhoneDevice::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
