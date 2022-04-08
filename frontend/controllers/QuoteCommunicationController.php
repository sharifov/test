<?php

namespace frontend\controllers;

use common\models\QuoteCommunication;
use common\models\search\QuoteCommunicationSearch;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * Class QuoteCommunicationController
 * @package frontend\controllers
 */
class QuoteCommunicationController extends FController
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
     * Lists all EmailQuote models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new QuoteCommunicationSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $qc_id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($qc_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($qc_id)
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new QuoteCommunication();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'qc_id' => $model->qc_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param $qc_id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($qc_id)
    {
        $model = $this->findModel($qc_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'qc_id' => $model->qc_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $qc_id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($qc_id)
    {
        $this->findModel($qc_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param $qc_id
     * @return QuoteCommunication|null
     * @throws NotFoundHttpException
     */
    protected function findModel($qc_id)
    {
        if (($model = QuoteCommunication::findOne(['qc_id' => $qc_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
