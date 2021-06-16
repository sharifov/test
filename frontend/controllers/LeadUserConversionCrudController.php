<?php

namespace frontend\controllers;

use Yii;
use sales\model\leadUserConversion\entity\LeadUserConversion;
use sales\model\leadUserConversion\entity\LeadUserConversionSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

/**
 * Class LeadUserConversionCrudController
 */
class LeadUserConversionCrudController extends FController
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
        $searchModel = new LeadUserConversionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $luc_lead_id
     * @param integer $luc_user_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($luc_lead_id, $luc_user_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($luc_lead_id, $luc_user_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new LeadUserConversion();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'luc_lead_id' => $model->luc_lead_id, 'luc_user_id' => $model->luc_user_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $luc_lead_id
     * @param integer $luc_user_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($luc_lead_id, $luc_user_id)
    {
        $model = $this->findModel($luc_lead_id, $luc_user_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'luc_lead_id' => $model->luc_lead_id, 'luc_user_id' => $model->luc_user_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $luc_lead_id
     * @param integer $luc_user_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($luc_lead_id, $luc_user_id): Response
    {
        $this->findModel($luc_lead_id, $luc_user_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $luc_lead_id
     * @param integer $luc_user_id
     * @return LeadUserConversion
     * @throws NotFoundHttpException
     */
    protected function findModel($luc_lead_id, $luc_user_id): LeadUserConversion
    {
        if (($model = LeadUserConversion::findOne(['luc_lead_id' => $luc_lead_id, 'luc_user_id' => $luc_user_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('LeadUserConversion not found');
    }
}
