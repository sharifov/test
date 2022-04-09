<?php

namespace modules\shiftSchedule\controllers;

use frontend\controllers\FController;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\shiftScheduleType\search\ShiftScheduleTypeSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ShiftScheduleTypeController implements the CRUD actions for ShiftScheduleType model.
 */
class ShiftScheduleTypeController extends FController
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete-ajax' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all ShiftScheduleType models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ShiftScheduleTypeSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ShiftScheduleType model.
     * @param int $sst_id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($sst_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($sst_id),
        ]);
    }

    /**
     * Creates a new ShiftScheduleType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ShiftScheduleType();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'sst_id' => $model->sst_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ShiftScheduleType model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $sst_id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($sst_id)
    {
        $model = $this->findModel($sst_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'sst_id' => $model->sst_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ShiftScheduleType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $sst_id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($sst_id)
    {
        $this->findModel($sst_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ShiftScheduleType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $sst_id ID
     * @return ShiftScheduleType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($sst_id)
    {
        if (($model = ShiftScheduleType::findOne(['sst_id' => $sst_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
