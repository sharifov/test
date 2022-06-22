<?php

namespace modules\objectSegment\controllers;

use frontend\controllers\FController;
use modules\objectSegment\src\entities\ObjectSegmentTask;
use modules\objectSegment\src\entities\search\ObjectSegmentTaskSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ObjectSegmentTaskController implements the CRUD actions for ObjectSegmentTask model.
 */
class ObjectSegmentTaskCrudController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

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
     * Lists all ObjectSegmentTask models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ObjectSegmentTaskSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ObjectSegmentTask model.
     * @param int $ostl_osl_id Ostl Osl ID
     * @param int $ostl_tl_id Ostl Tl ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ostl_osl_id, $ostl_tl_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($ostl_osl_id, $ostl_tl_id),
        ]);
    }

    /**
     * Creates a new ObjectSegmentTask model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ObjectSegmentTask();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'ostl_osl_id' => $model->ostl_osl_id, 'ostl_tl_id' => $model->ostl_tl_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ObjectSegmentTask model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $ostl_osl_id Ostl Osl ID
     * @param int $ostl_tl_id Ostl Tl ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($ostl_osl_id, $ostl_tl_id)
    {
        $model = $this->findModel($ostl_osl_id, $ostl_tl_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ostl_osl_id' => $model->ostl_osl_id, 'ostl_tl_id' => $model->ostl_tl_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ObjectSegmentTask model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $ostl_osl_id Ostl Osl ID
     * @param int $ostl_tl_id Ostl Tl ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($ostl_osl_id, $ostl_tl_id)
    {
        $this->findModel($ostl_osl_id, $ostl_tl_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ObjectSegmentTask model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $ostl_osl_id Ostl Osl ID
     * @param int $ostl_tl_id Ostl Tl ID
     * @return ObjectSegmentTask the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ostl_osl_id, $ostl_tl_id)
    {
        if (($model = ObjectSegmentTask::findOne(['ostl_osl_id' => $ostl_osl_id, 'ostl_tl_id' => $ostl_tl_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException("Not found ObjectSegmentTask by ({$ostl_osl_id}, {$ostl_tl_id})");
    }
}
