<?php

namespace frontend\controllers;

use common\models\QuoteUrlActivity;
use yii\filters\VerbFilter;
use common\models\search\QuoteUrlActivitySearch;
use yii\web\NotFoundHttpException;

/**
 * Class QuoteUrlActivityController
 * @package frontend\controllers
 */
class QuoteUrlActivityController extends FController
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
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new QuoteUrlActivitySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $qua_id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($qua_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($qua_id)
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new QuoteUrlActivity();

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'qua_id' => $model->qua_id]);
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param $qua_id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($qua_id)
    {
        $model = $this->findModel($qua_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'qua_id' => $model->qua_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $qua_id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($qua_id)
    {
        $this->findModel($qua_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param $qua_id
     * @return QuoteUrlActivity|null
     * @throws NotFoundHttpException
     */
    protected function findModel($qua_id)
    {
        if (($model = QuoteUrlActivity::findOne(['qua_id' => $qua_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
