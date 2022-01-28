<?php

namespace frontend\controllers;

use Yii;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingData;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\ArrayHelper;

class LeadPoorProcessingDataCrudController extends FController
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
        $searchModel = new LeadPoorProcessingDataSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $lppd_id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($lppd_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($lppd_id),
        ]);
    }

    /**
     * @param int $lppd_id ID
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($lppd_id)
    {
        $model = $this->findModel($lppd_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'lppd_id' => $model->lppd_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $lppd_id ID
     * @return LeadPoorProcessingData
     * @throws NotFoundHttpException
     */
    protected function findModel($lppd_id): LeadPoorProcessingData
    {
        if (($model = LeadPoorProcessingData::findOne($lppd_id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('LeadPoorProcessingData not found by ID(' .  $lppd_id . ')');
    }
}
