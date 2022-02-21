<?php

namespace frontend\controllers;

use src\services\cleaner\cleaners\LeadPoorProcessingLogCleaner;
use src\services\cleaner\form\DbCleanerParamsForm;
use Yii;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLog;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

class LeadPoorProcessingLogCrudController extends FController
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
        $searchModel = new LeadPoorProcessingLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $cleaner = new LeadPoorProcessingLogCleaner();
        $dbCleanerParamsForm = (new DbCleanerParamsForm())
            ->setTable($cleaner->getTable())
            ->setColumn($cleaner->getColumn());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'modelCleaner' => $dbCleanerParamsForm,
        ]);
    }

    /**
     * @param int $lppl_id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($lppl_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($lppl_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new LeadPoorProcessingLog();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'lppl_id' => $model->lppl_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $lppl_id ID
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($lppl_id)
    {
        $model = $this->findModel($lppl_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'lppl_id' => $model->lppl_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $lppl_id ID
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($lppl_id): Response
    {
        $this->findModel($lppl_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param int $lppl_id ID
     * @return LeadPoorProcessingLog
     * @throws NotFoundHttpException
     */
    protected function findModel($lppl_id): LeadPoorProcessingLog
    {
        if (($model = LeadPoorProcessingLog::findOne($lppl_id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('LeadPoorProcessingLog not found by ID(' . $lppl_id . ')');
    }
}
