<?php

namespace frontend\controllers;

use common\models\QuoteTrip;
use common\models\search\QuoteSegmentSearch;
use common\models\search\QuoteTripSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * QuoteTripCrudController implements the CRUD actions for QuoteTrip model.
 */
class QuoteTripCrudController extends FController
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
     * Lists all QuoteTrip models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new QuoteTripSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single QuoteTrip model.
     * @param int $qt_id Qt ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($qt_id)
    {
        $searchModel = new QuoteSegmentSearch();
        $queryParams = $this->request->queryParams;
        $queryParams['QuoteSegmentSearch']['qs_trip_id'] = $qt_id;
        $dataProvider = $searchModel->search($queryParams);

        return $this->render('view', [
            'model' => $this->findModel($qt_id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Creates a new QuoteTrip model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new QuoteTrip();
        $model->setScenario(QuoteTrip::SCENARIO_CRUD);

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'qt_id' => $model->qt_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing QuoteTrip model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $qt_id Qt ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($qt_id)
    {
        $model = $this->findModel($qt_id);
        $model->setScenario(QuoteTrip::SCENARIO_CRUD);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'qt_id' => $model->qt_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing QuoteTrip model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $qt_id Qt ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($qt_id)
    {
        $this->findModel($qt_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the QuoteTrip model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $qt_id Qt ID
     * @return QuoteTrip the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($qt_id)
    {
        if (($model = QuoteTrip::findOne(['qt_id' => $qt_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Quote Trip not found by ID(' . $qt_id . ')');
    }
}
