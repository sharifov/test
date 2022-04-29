<?php

namespace modules\shiftSchedule\controllers;

use frontend\controllers\FController;
use modules\shiftSchedule\src\entities\shiftScheduleTypeLabelAssign\ShiftScheduleTypeLabelAssign;
use modules\shiftSchedule\src\entities\shiftScheduleTypeLabelAssign\search\ShiftScheduleTypeLabelAssignSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ShiftScheduleTypeLabelAssignController implements the CRUD actions for ShiftScheduleTypeLabelAssign model.
 */
class ShiftScheduleTypeLabelAssignController extends FController
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
     * Lists all ShiftScheduleTypeLabelAssign models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ShiftScheduleTypeLabelAssignSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ShiftScheduleTypeLabelAssign model.
     * @param string $tla_stl_key Label Key
     * @param int $tla_sst_id Shift Type ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($tla_stl_key, $tla_sst_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($tla_stl_key, $tla_sst_id),
        ]);
    }

    /**
     * Creates a new ShiftScheduleTypeLabelAssign model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ShiftScheduleTypeLabelAssign();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'tla_stl_key' => $model->tla_stl_key, 'tla_sst_id' => $model->tla_sst_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ShiftScheduleTypeLabelAssign model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $tla_stl_key Label Key
     * @param int $tla_sst_id Shift Type ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($tla_stl_key, $tla_sst_id)
    {
        $model = $this->findModel($tla_stl_key, $tla_sst_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'tla_stl_key' => $model->tla_stl_key, 'tla_sst_id' => $model->tla_sst_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ShiftScheduleTypeLabelAssign model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $tla_stl_key Label Key
     * @param int $tla_sst_id Shift Type ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($tla_stl_key, $tla_sst_id)
    {
        $this->findModel($tla_stl_key, $tla_sst_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ShiftScheduleTypeLabelAssign model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $tla_stl_key Label Key
     * @param int $tla_sst_id Shift Type ID
     * @return ShiftScheduleTypeLabelAssign the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($tla_stl_key, $tla_sst_id)
    {
        if (($model = ShiftScheduleTypeLabelAssign::findOne(['tla_stl_key' => $tla_stl_key, 'tla_sst_id' => $tla_sst_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
