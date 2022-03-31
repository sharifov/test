<?php

namespace frontend\controllers;

use src\model\emailQuote\entity\EmailQuote;
use src\model\emailQuote\entity\EmailQuoteSearch;
use frontend\controllers\FController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EmailQuoteCrudController implements the CRUD actions for EmailQuote model.
 */
class EmailQuoteCrudController extends FController
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
     * Lists all EmailQuote models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new EmailQuoteSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EmailQuote model.
     * @param int $eq_id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($eq_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($eq_id),
        ]);
    }

    /**
     * Creates a new EmailQuote model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new EmailQuote();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'eq_id' => $model->eq_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EmailQuote model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $eq_id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($eq_id)
    {
        $model = $this->findModel($eq_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'eq_id' => $model->eq_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing EmailQuote model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $eq_id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($eq_id)
    {
        $this->findModel($eq_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the EmailQuote model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $eq_id ID
     * @return EmailQuote the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($eq_id)
    {
        if (($model = EmailQuote::findOne(['eq_id' => $eq_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
