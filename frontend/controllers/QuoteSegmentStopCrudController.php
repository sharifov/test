<?php

namespace frontend\controllers;

use common\models\QuoteSegmentStop;
use common\models\search\QuoteSegmentStopSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * QuoteSegmentStopCrudController implements the CRUD actions for QuoteSegmentStop model.
 */
class QuoteSegmentStopCrudController extends FController
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
     * Lists all QuoteSegmentStop models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new QuoteSegmentStopSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single QuoteSegmentStop model.
     * @param int $qss_id Qss ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($qss_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($qss_id),
        ]);
    }

    /**
     * Creates a new QuoteSegmentStop model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new QuoteSegmentStop();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'qss_id' => $model->qss_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing QuoteSegmentStop model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $qss_id Qss ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($qss_id)
    {
        $model = $this->findModel($qss_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'qss_id' => $model->qss_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing QuoteSegmentStop model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $qss_id Qss ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($qss_id)
    {
        $this->findModel($qss_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the QuoteSegmentStop model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $qss_id Qss ID
     * @return QuoteSegmentStop the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($qss_id)
    {
        if (($model = QuoteSegmentStop::findOne(['qss_id' => $qss_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
