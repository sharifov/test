<?php

namespace frontend\controllers;

use Yii;
use sales\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfo;
use sales\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

class ContactPhoneServiceInfoCrudController extends FController
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
        $searchModel = new ContactPhoneServiceInfoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $cpsi_cpl_id
     * @param integer $cpsi_service_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($cpsi_cpl_id, $cpsi_service_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($cpsi_cpl_id, $cpsi_service_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ContactPhoneServiceInfo();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cpsi_cpl_id' => $model->cpsi_cpl_id, 'cpsi_service_id' => $model->cpsi_service_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $cpsi_cpl_id
     * @param integer $cpsi_service_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($cpsi_cpl_id, $cpsi_service_id)
    {
        $model = $this->findModel($cpsi_cpl_id, $cpsi_service_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cpsi_cpl_id' => $model->cpsi_cpl_id, 'cpsi_service_id' => $model->cpsi_service_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $cpsi_cpl_id
     * @param integer $cpsi_service_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($cpsi_cpl_id, $cpsi_service_id): Response
    {
        $this->findModel($cpsi_cpl_id, $cpsi_service_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $cpsi_cpl_id
     * @param integer $cpsi_service_id
     * @return ContactPhoneServiceInfo
     * @throws NotFoundHttpException
     */
    protected function findModel($cpsi_cpl_id, $cpsi_service_id): ContactPhoneServiceInfo
    {
        if (($model = ContactPhoneServiceInfo::findOne(['cpsi_cpl_id' => $cpsi_cpl_id, 'cpsi_service_id' => $cpsi_service_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('ContactPhoneServiceInfo not found');
    }
}
