<?php

namespace modules\shiftSchedule\controllers;

use modules\shiftSchedule\src\entities\shiftScheduleRequestHistory\ShiftScheduleRequestHistory;
use modules\shiftSchedule\src\entities\shiftScheduleRequestHistory\search\ShiftScheduleRequestHistorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ShiftScheduleRequestHistoryController implements the CRUD actions for ShiftScheduleRequestHistory model.
 */
class ShiftScheduleRequestHistoryController extends Controller
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
     * Lists all ShiftScheduleRequestHistory models.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new ShiftScheduleRequestHistorySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ShiftScheduleRequestHistory model.
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
     * Creates a new ShiftScheduleRequestHistory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ShiftScheduleRequestHistory();

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
     * Updates an existing ShiftScheduleRequestHistory model.
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
     * Deletes an existing ShiftScheduleRequestHistory model.
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
     * Finds the ShiftScheduleRequestHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $ssrh_id Ssrh ID
     * @return ShiftScheduleRequestHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $ssrh_id): ShiftScheduleRequestHistory
    {
        if (($model = ShiftScheduleRequestHistory::findOne(['ssrh_id' => $ssrh_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
