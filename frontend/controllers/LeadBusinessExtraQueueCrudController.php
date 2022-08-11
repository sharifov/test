<?php

namespace frontend\controllers;

use src\model\leadBusinessExtraQueue\entity\LeadBusinessExtraQueue;
use src\model\leadBusinessExtraQueue\entity\LeadBusinessExtraQueueSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LeadBusinessExtraQueueCrudController implements the CRUD actions for LeadBusinessExtraQueue model.
 */
class LeadBusinessExtraQueueCrudController extends Controller
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
     * Lists all LeadBusinessExtraQueue models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LeadBusinessExtraQueueSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LeadBusinessExtraQueue model.
     * @param int $lbeq_lead_id Lead
     * @param int $lbeq_lbeqr_id LeadBusinessExtraQueue
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($lbeq_lead_id, $lbeq_lbeqr_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($lbeq_lead_id, $lbeq_lbeqr_id),
        ]);
    }

    /**
     * Creates a new LeadBusinessExtraQueue model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new LeadBusinessExtraQueue();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'lbeq_lead_id' => $model->lbeq_lead_id, 'lbeq_lbeqr_id' => $model->lbeq_lbeqr_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing LeadBusinessExtraQueue model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $lbeq_lead_id Lead
     * @param int $lbeq_lbeqr_id LeadBusinessExtraQueue
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($lbeq_lead_id, $lbeq_lbeqr_id)
    {
        $model = $this->findModel($lbeq_lead_id, $lbeq_lbeqr_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'lbeq_lead_id' => $model->lbeq_lead_id, 'lbeq_lbeqr_id' => $model->lbeq_lbeqr_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing LeadBusinessExtraQueue model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $lbeq_lead_id Lead
     * @param int $lbeq_lbeqr_id LeadBusinessExtraQueue
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($lbeq_lead_id, $lbeq_lbeqr_id)
    {
        $this->findModel($lbeq_lead_id, $lbeq_lbeqr_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the LeadBusinessExtraQueue model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $lbeq_lead_id Lead
     * @param int $lbeq_lbeqr_id LeadBusinessExtraQueue
     * @return LeadBusinessExtraQueue the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($lbeq_lead_id, $lbeq_lbeqr_id)
    {
        if (($model = LeadBusinessExtraQueue::findOne(['lbeq_lead_id' => $lbeq_lead_id, 'lbeq_lbeqr_id' => $lbeq_lbeqr_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
