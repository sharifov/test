<?php

namespace modules\shiftSchedule\controllers;

use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\search\ShiftScheduleRequestSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ShiftScheduleRequestController implements the CRUD actions for ShiftScheduleRequest model.
 */
class ShiftScheduleRequestController extends Controller
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
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all ShiftScheduleRequest models.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new ShiftScheduleRequestSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ShiftScheduleRequest model.
     * @param int $srh_id Srh ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $srh_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($srh_id),
        ]);
    }

    /**
     * Creates a new ShiftScheduleRequest model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ShiftScheduleRequest();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'srh_id' => $model->srh_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ShiftScheduleRequest model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $srh_id Srh ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $srh_id)
    {
        $model = $this->findModel($srh_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'srh_id' => $model->srh_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ShiftScheduleRequest model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $srh_id Srh ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(int $srh_id): Response
    {
        $this->findModel($srh_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ShiftScheduleRequest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $srh_id Srh ID
     * @return ShiftScheduleRequest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $srh_id): ShiftScheduleRequest
    {
        if (($model = ShiftScheduleRequest::findOne(['srh_id' => $srh_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
