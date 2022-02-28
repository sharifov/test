<?php

namespace frontend\controllers;

use src\model\leadStatusReasonLog\entity\LeadStatusReasonLog;
use src\model\leadStatusReasonLog\entity\LeadStatusReasonLogSearch;
use frontend\controllers\FController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LeadStatusReasonLogCrudController implements the CRUD actions for LeadStatusReasonLog model.
 */
class LeadStatusReasonLogCrudController extends FController
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
     * Lists all LeadStatusReasonLog models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LeadStatusReasonLogSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LeadStatusReasonLog model.
     * @param int $lsrl_id Lsrl ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($lsrl_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($lsrl_id),
        ]);
    }

    /**
     * Creates a new LeadStatusReasonLog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new LeadStatusReasonLog();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'lsrl_id' => $model->lsrl_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing LeadStatusReasonLog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $lsrl_id Lsrl ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($lsrl_id)
    {
        $model = $this->findModel($lsrl_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'lsrl_id' => $model->lsrl_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing LeadStatusReasonLog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $lsrl_id Lsrl ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($lsrl_id)
    {
        $this->findModel($lsrl_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the LeadStatusReasonLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $lsrl_id Lsrl ID
     * @return LeadStatusReasonLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($lsrl_id)
    {
        if (($model = LeadStatusReasonLog::findOne(['lsrl_id' => $lsrl_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
