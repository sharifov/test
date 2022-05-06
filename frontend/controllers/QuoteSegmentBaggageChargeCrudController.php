<?php

namespace frontend\controllers;

use common\models\Currency;
use common\models\QuoteSegmentBaggageCharge;
use common\models\search\QuoteSegmentBaggageChargeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * QuoteSegmentBaggageChargeCrudController implements the CRUD actions for QuoteSegmentBaggageCharge model.
 */
class QuoteSegmentBaggageChargeCrudController extends Controller
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
     * Lists all QuoteSegmentBaggageCharge models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new QuoteSegmentBaggageChargeSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single QuoteSegmentBaggageCharge model.
     * @param int $qsbc_id Qsbc ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($qsbc_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($qsbc_id),
        ]);
    }

    /**
     * Creates a new QuoteSegmentBaggageCharge model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new QuoteSegmentBaggageCharge();
        $model->qsbc_currency = Currency::getDefaultCurrencyCodeByDb();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'qsbc_id' => $model->qsbc_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing QuoteSegmentBaggageCharge model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $qsbc_id Qsbc ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($qsbc_id)
    {
        $model = $this->findModel($qsbc_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'qsbc_id' => $model->qsbc_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing QuoteSegmentBaggageCharge model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $qsbc_id Qsbc ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($qsbc_id)
    {
        $this->findModel($qsbc_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the QuoteSegmentBaggageCharge model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $qsbc_id Qsbc ID
     * @return QuoteSegmentBaggageCharge the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($qsbc_id)
    {
        if (($model = QuoteSegmentBaggageCharge::findOne(['qsbc_id' => $qsbc_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
