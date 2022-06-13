<?php

namespace frontend\controllers;

use common\components\experimentManager\models\ExperimentTarget;
use common\models\search\ExperimentTargetSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ExperimentTargetCrudController implements the CRUD actions for ExperimentTarget model.
 */
class ExperimentTargetCrudController extends Controller
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
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all ExperimentTarget models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ExperimentTargetSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ExperimentTarget model.
     * @param int $ext_id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ext_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($ext_id),
        ]);
    }

    /**
     * Creates a new ExperimentTarget model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ExperimentTarget();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'ext_id' => $model->ext_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ExperimentTarget model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $ext_id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($ext_id)
    {
        $model = $this->findModel($ext_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ext_id' => $model->ext_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ExperimentTarget model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $ext_id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($ext_id)
    {
        $this->findModel($ext_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ExperimentTarget model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $ext_id ID
     * @return ExperimentTarget the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ext_id)
    {
        if (($model = ExperimentTarget::findOne(['ext_id' => $ext_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
