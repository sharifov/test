<?php

namespace modules\shiftSchedule\controllers;

use modules\shiftSchedule\src\entities\userShiftScheduleLog\UserShiftScheduleLog;
use modules\shiftSchedule\src\entities\userShiftScheduleLog\search\UserShiftScheduleLogSearch;
use frontend\controllers\FController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserShiftScheduleLogCrudController implements the CRUD actions for UserShiftScheduleLog model.
 */
class UserShiftScheduleLogCrudController extends FController
{
    /**
     * @inheritDoc
     */
    public function behaviors()
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
     * Lists all UserShiftScheduleLog models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserShiftScheduleLogSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserShiftScheduleLog model.
     * @param int $ussl_id ID
     * @param int $ussl_month_start Month Start
     * @param int $ussl_year_start Year Start
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ussl_id, $ussl_month_start, $ussl_year_start)
    {
        return $this->render('view', [
            'model' => $this->findModel($ussl_id, $ussl_month_start, $ussl_year_start),
        ]);
    }

    /**
     * Creates a new UserShiftScheduleLog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new UserShiftScheduleLog();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'ussl_id' => $model->ussl_id, 'ussl_month_start' => $model->ussl_month_start, 'ussl_year_start' => $model->ussl_year_start]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserShiftScheduleLog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $ussl_id ID
     * @param int $ussl_month_start Month Start
     * @param int $ussl_year_start Year Start
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($ussl_id, $ussl_month_start, $ussl_year_start)
    {
        $model = $this->findModel($ussl_id, $ussl_month_start, $ussl_year_start);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ussl_id' => $model->ussl_id, 'ussl_month_start' => $model->ussl_month_start, 'ussl_year_start' => $model->ussl_year_start]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserShiftScheduleLog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $ussl_id ID
     * @param int $ussl_month_start Month Start
     * @param int $ussl_year_start Year Start
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($ussl_id, $ussl_month_start, $ussl_year_start)
    {
        $this->findModel($ussl_id, $ussl_month_start, $ussl_year_start)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserShiftScheduleLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $ussl_id ID
     * @param int $ussl_month_start Month Start
     * @param int $ussl_year_start Year Start
     * @return UserShiftScheduleLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ussl_id, $ussl_month_start, $ussl_year_start)
    {
        if (($model = UserShiftScheduleLog::findOne(['ussl_id' => $ussl_id, 'ussl_month_start' => $ussl_month_start, 'ussl_year_start' => $ussl_year_start])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
