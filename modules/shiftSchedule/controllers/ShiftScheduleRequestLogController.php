<?php

namespace modules\shiftSchedule\controllers;

use modules\shiftSchedule\src\entities\shiftScheduleRequestLog\ShiftScheduleRequestLog;
use modules\shiftSchedule\src\entities\shiftScheduleRequestLog\search\ShiftScheduleRequestLogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ShiftScheduleRequestLogController implements the CRUD actions for ShiftScheduleRequestLog model.
 */
class ShiftScheduleRequestLogController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all ShiftScheduleRequestLog models.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new ShiftScheduleRequestLogSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ShiftScheduleRequestLog model.
     * @param int $ssrh_id Ssrh ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $ssrh_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($ssrh_id),
        ]);
    }

    /**
     * Creates a new ShiftScheduleRequestLog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ShiftScheduleRequestLog();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'ssrh_id' => $model->ssrh_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ShiftScheduleRequestLog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $ssrh_id Ssrh ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $ssrh_id)
    {
        $model = $this->findModel($ssrh_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ssrh_id' => $model->ssrh_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ShiftScheduleRequestLog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $ssrh_id Ssrh ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(int $ssrh_id): Response
    {
        $this->findModel($ssrh_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ShiftScheduleRequestLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $ssrh_id Ssrh ID
     * @return ShiftScheduleRequestLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $ssrh_id): ShiftScheduleRequestLog
    {
        if (($model = ShiftScheduleRequestLog::findOne(['ssrh_id' => $ssrh_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
