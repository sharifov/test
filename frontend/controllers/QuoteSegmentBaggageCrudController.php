<?php

namespace frontend\controllers;

use common\models\Currency;
use common\models\QuoteSegmentBaggage;
use common\models\search\QuoteSegmentBaggageSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * QuoteSegmentBaggageCrudController implements the CRUD actions for QuoteSegmentBaggage model.
 */
class QuoteSegmentBaggageCrudController extends Controller
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
     * Lists all QuoteSegmentBaggage models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new QuoteSegmentBaggageSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single QuoteSegmentBaggage model.
     * @param int $qsb_id Qsb ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($qsb_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($qsb_id),
        ]);
    }

    /**
     * Creates a new QuoteSegmentBaggage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new QuoteSegmentBaggage();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'qsb_id' => $model->qsb_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing QuoteSegmentBaggage model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $qsb_id Qsb ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($qsb_id)
    {
        $model = $this->findModel($qsb_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'qsb_id' => $model->qsb_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing QuoteSegmentBaggage model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $qsb_id Qsb ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($qsb_id)
    {
        $this->findModel($qsb_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the QuoteSegmentBaggage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $qsb_id Qsb ID
     * @return QuoteSegmentBaggage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($qsb_id)
    {
        if (($model = QuoteSegmentBaggage::findOne(['qsb_id' => $qsb_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
