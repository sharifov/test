<?php

namespace frontend\controllers;

use common\models\QuoteSearchCid;
use common\models\search\QuoteSearchCidSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * QuoteSearchCidController implements the CRUD actions for QuoteSearchCid model.
 */
class QuoteSearchCidController extends FController
{
    /**
     * @inheritDoc
     */
    public function behaviors(): array
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
     * Lists all QuoteSearchCid models.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new QuoteSearchCidSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single QuoteSearchCid model.
     * @param int $qsc_id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($qsc_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($qsc_id),
        ]);
    }

    /**
     * Creates a new QuoteSearchCid model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new QuoteSearchCid();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'qsc_id' => $model->qsc_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing QuoteSearchCid model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $qsc_id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($qsc_id)
    {
        $model = $this->findModel($qsc_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'qsc_id' => $model->qsc_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing QuoteSearchCid model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $qsc_id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($qsc_id)
    {
        $this->findModel($qsc_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the QuoteSearchCid model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $qsc_id ID
     * @return QuoteSearchCid the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($qsc_id)
    {
        if (($model = QuoteSearchCid::findOne(['qsc_id' => $qsc_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
