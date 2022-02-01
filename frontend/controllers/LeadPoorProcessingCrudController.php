<?php

namespace frontend\controllers;

use Yii;
use src\model\leadPoorProcessing\entity\LeadPoorProcessing;
use src\model\leadPoorProcessing\entity\LeadPoorProcessingSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

class LeadPoorProcessingCrudController extends FController
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
        $searchModel = new LeadPoorProcessingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $lpp_lead_id Lead
     * @param int $lpp_lppd_id LeadPoorProcessingData
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($lpp_lead_id, $lpp_lppd_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($lpp_lead_id, $lpp_lppd_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new LeadPoorProcessing();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'lpp_lead_id' => $model->lpp_lead_id, 'lpp_lppd_id' => $model->lpp_lppd_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $lpp_lead_id Lead
     * @param int $lpp_lppd_id LeadPoorProcessingData
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($lpp_lead_id, $lpp_lppd_id)
    {
        $model = $this->findModel($lpp_lead_id, $lpp_lppd_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'lpp_lead_id' => $model->lpp_lead_id, 'lpp_lppd_id' => $model->lpp_lppd_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $lpp_lead_id Lead
     * @param int $lpp_lppd_id LeadPoorProcessingData
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($lpp_lead_id, $lpp_lppd_id): Response
    {
        $this->findModel($lpp_lead_id, $lpp_lppd_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param int $lpp_lead_id Lead
     * @param int $lpp_lppd_id LeadPoorProcessingData
     * @return LeadPoorProcessing
     * @throws NotFoundHttpException
     */
    protected function findModel($lpp_lead_id, $lpp_lppd_id): LeadPoorProcessing
    {
        if (($model = LeadPoorProcessing::findOne(['lpp_lead_id' => $lpp_lead_id, 'lpp_lppd_id' => $lpp_lppd_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('LeadPoorProcessing not found');
    }
}
