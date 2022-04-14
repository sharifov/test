<?php

namespace frontend\controllers;

use modules\shiftSchedule\src\entities\shiftCategory\search\ShiftCategorySearch;
use modules\shiftSchedule\src\entities\shiftCategory\ShiftCategory;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * ShiftCategoryCrudController implements the CRUD actions for ShiftCategory model.
 */
class ShiftCategoryCrudController extends FController
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
     * Lists all ShiftCategory models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ShiftCategorySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ShiftCategory model.
     * @param int $sc_id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($sc_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($sc_id),
        ]);
    }

    /**
     * Creates a new ShiftCategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ShiftCategory();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'sc_id' => $model->sc_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ShiftCategory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $sc_id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($sc_id)
    {
        $model = $this->findModel($sc_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'sc_id' => $model->sc_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ShiftCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $sc_id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($sc_id)
    {
        $this->findModel($sc_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ShiftCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $sc_id ID
     * @return ShiftCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($sc_id)
    {
        if (($model = ShiftCategory::findOne(['sc_id' => $sc_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
