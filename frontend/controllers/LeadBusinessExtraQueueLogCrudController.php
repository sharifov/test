<?php

namespace frontend\controllers;

use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLog;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LeadBusinessExtraQueueLogCrudController implements the CRUD actions for LeadBusinessExtraQueueLog model.
 */
class LeadBusinessExtraQueueLogCrudController extends Controller
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
     * Lists all LeadBusinessExtraQueueLog models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LeadBusinessExtraQueueLogSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LeadBusinessExtraQueueLog model.
     * @param int $lbeql_id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($lbeql_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($lbeql_id),
        ]);
    }

    /**
     * Creates a new LeadBusinessExtraQueueLog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new LeadBusinessExtraQueueLog();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'lbeql_id' => $model->lbeql_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing LeadBusinessExtraQueueLog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $lbeql_id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($lbeql_id)
    {
        $model = $this->findModel($lbeql_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'lbeql_id' => $model->lbeql_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing LeadBusinessExtraQueueLog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $lbeql_id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($lbeql_id)
    {
        $this->findModel($lbeql_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the LeadBusinessExtraQueueLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $lbeql_id ID
     * @return LeadBusinessExtraQueueLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($lbeql_id)
    {
        if (($model = LeadBusinessExtraQueueLog::findOne(['lbeql_id' => $lbeql_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
