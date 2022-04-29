<?php

namespace modules\shiftSchedule\controllers;

use frontend\controllers\FController;
use modules\shiftSchedule\src\entities\shiftScheduleTypeLabel\ShiftScheduleTypeLabel;
use modules\shiftSchedule\src\entities\shiftScheduleTypeLabel\search\ShiftScheduleTypeLabelSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ShiftScheduleTypeLabelController implements the CRUD actions for ShiftScheduleTypeLabel model.
 */
class ShiftScheduleTypeLabelController extends FController
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
     * Lists all ShiftScheduleTypeLabel models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ShiftScheduleTypeLabelSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ShiftScheduleTypeLabel model.
     * @param string $stl_key ID/Key
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($stl_key)
    {
        return $this->render('view', [
            'model' => $this->findModel($stl_key),
        ]);
    }

    /**
     * Creates a new ShiftScheduleTypeLabel model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ShiftScheduleTypeLabel();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'stl_key' => $model->stl_key]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ShiftScheduleTypeLabel model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $stl_key ID/Key
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($stl_key)
    {
        $model = $this->findModel($stl_key);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'stl_key' => $model->stl_key]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ShiftScheduleTypeLabel model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $stl_key ID/Key
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($stl_key)
    {
        $this->findModel($stl_key)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ShiftScheduleTypeLabel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $stl_key ID/Key
     * @return ShiftScheduleTypeLabel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($stl_key)
    {
        if (($model = ShiftScheduleTypeLabel::findOne(['stl_key' => $stl_key])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
