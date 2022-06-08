<?php

namespace frontend\controllers;

use common\models\QuoteSegment;
use common\models\search\QuoteSegmentSearch;
use common\models\search\QuoteSegmentStopSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * QuoteSegmentCrudController implements the CRUD actions for QuoteSegment model.
 */
class QuoteSegmentCrudController extends FController
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
     * Lists all QuoteSegment models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new QuoteSegmentSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single QuoteSegment model.
     * @param int $qs_id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($qs_id)
    {
        $searchModel = new QuoteSegmentStopSearch();
        $queryParams = $this->request->queryParams;
        $queryParams['QuoteSegmentStopSearch']['qss_segment_id'] = $qs_id;
        $dataProvider = $searchModel->search($queryParams);

        return $this->render('view', [
            'model' => $this->findModel($qs_id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Creates a new QuoteSegment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new QuoteSegment();
        $model->setScenario(QuoteSegment::SCENARIO_CRUD);

        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->generateKey();
            if ($model->save()) {
                return $this->redirect(['view', 'qs_id' => $model->qs_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing QuoteSegment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $qs_id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($qs_id)
    {
        $model = $this->findModel($qs_id);
        $model->setScenario(QuoteSegment::SCENARIO_CRUD);

        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->generateKey();
            if ($model->save()) {
                return $this->redirect(['view', 'qs_id' => $model->qs_id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing QuoteSegment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $qs_id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($qs_id)
    {
        $this->findModel($qs_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the QuoteSegment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $qs_id ID
     * @return QuoteSegment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($qs_id)
    {
        if (($model = QuoteSegment::findOne(['qs_id' => $qs_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Quote Segment not found by ID(' . $qs_id . ')');
    }
}
