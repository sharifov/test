<?php

namespace frontend\controllers;

use src\model\leadStatusReason\entity\LeadStatusReason;
use src\model\leadStatusReason\entity\LeadStatusReasonSearch;
use frontend\controllers\FController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LeadStatusReasonCrudController implements the CRUD actions for LeadStatusReason model.
 */
class LeadStatusReasonCrudController extends FController
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
     * Lists all LeadStatusReason models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LeadStatusReasonSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LeadStatusReason model.
     * @param int $lsr_id Lsr ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($lsr_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($lsr_id),
        ]);
    }

    /**
     * Creates a new LeadStatusReason model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new LeadStatusReason();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'lsr_id' => $model->lsr_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing LeadStatusReason model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $lsr_id Lsr ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($lsr_id)
    {
        $model = $this->findModel($lsr_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'lsr_id' => $model->lsr_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing LeadStatusReason model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $lsr_id Lsr ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($lsr_id)
    {
        $this->findModel($lsr_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the LeadStatusReason model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $lsr_id Lsr ID
     * @return LeadStatusReason the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($lsr_id)
    {
        if (($model = LeadStatusReason::findOne(['lsr_id' => $lsr_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
