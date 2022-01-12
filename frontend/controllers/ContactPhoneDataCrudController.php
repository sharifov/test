<?php

namespace frontend\controllers;

use Yii;
use src\model\contactPhoneData\entity\ContactPhoneData;
use src\model\contactPhoneData\entity\ContactPhoneDataSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

/**
 * Class ContactPhoneDataCrudController
 */
class ContactPhoneDataCrudController extends FController
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
        $searchModel = new ContactPhoneDataSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $cpd_cpl_id
     * @param string $cpd_key
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($cpd_cpl_id, $cpd_key): string
    {
        return $this->render('view', [
            'model' => $this->findModel($cpd_cpl_id, $cpd_key),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ContactPhoneData();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cpd_cpl_id' => $model->cpd_cpl_id, 'cpd_key' => $model->cpd_key]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $cpd_cpl_id
     * @param string $cpd_key
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($cpd_cpl_id, $cpd_key)
    {
        $model = $this->findModel($cpd_cpl_id, $cpd_key);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cpd_cpl_id' => $model->cpd_cpl_id, 'cpd_key' => $model->cpd_key]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $cpd_cpl_id
     * @param string $cpd_key
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($cpd_cpl_id, $cpd_key): Response
    {
        $this->findModel($cpd_cpl_id, $cpd_key)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $cpd_cpl_id
     * @param string $cpd_key
     * @return ContactPhoneData
     * @throws NotFoundHttpException
     */
    protected function findModel($cpd_cpl_id, $cpd_key): ContactPhoneData
    {
        if (($model = ContactPhoneData::findOne(['cpd_cpl_id' => $cpd_cpl_id, 'cpd_key' => $cpd_key])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('ContactPhoneData not found.');
    }
}
