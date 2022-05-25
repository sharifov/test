<?php

namespace frontend\controllers;

use common\models\QuoteCommunicationOpenLog;
use common\models\search\QuoteCommunicationOpenLogSearch;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * Class QuoteCommunicationOpenLogController
 * @package frontend\controllers
 */
class QuoteCommunicationOpenLogController extends FController
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
     * Lists all QuoteCommunication models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new QuoteCommunicationOpenLogSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $qcol_id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($qcol_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($qcol_id)
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new QuoteCommunicationOpenLog();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'qcol_id' => $model->qcol_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param $qcol_id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($qcol_id)
    {
        $model = $this->findModel($qcol_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'qcol_id' => $model->qcol_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $qcol_id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($qcol_id)
    {
        $this->findModel($qcol_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param $qcol_id
     * @return QuoteCommunicationOpenLog|null
     * @throws NotFoundHttpException
     */
    protected function findModel($qcol_id): ?QuoteCommunicationOpenLog
    {
        if (($model = QuoteCommunicationOpenLog::findOne(['qcol_id' => $qcol_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
