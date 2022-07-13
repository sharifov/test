<?php

namespace frontend\controllers;

use src\model\leadBusinessExtraQueueRule\entity\LeadBusinessExtraQueueRule;
use src\model\leadBusinessExtraQueueRule\entity\LeadBusinessExtraQueueRuleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LeadBusinessExtraQueueRuleCrudController implements the CRUD actions for LeadBusinessExtraQueueRule model.
 */
class LeadBusinessExtraQueueRuleCrudController extends Controller
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
     * Lists all LeadBusinessExtraQueueRule models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LeadBusinessExtraQueueRuleSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LeadBusinessExtraQueueRule model.
     * @param int $lbeqr_id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($lbeqr_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($lbeqr_id),
        ]);
    }

    /**
     * Creates a new LeadBusinessExtraQueueRule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new LeadBusinessExtraQueueRule();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'lbeqr_id' => $model->lbeqr_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing LeadBusinessExtraQueueRule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $lbeqr_id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($lbeqr_id)
    {
        $model = $this->findModel($lbeqr_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'lbeqr_id' => $model->lbeqr_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing LeadBusinessExtraQueueRule model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $lbeqr_id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($lbeqr_id)
    {
        $this->findModel($lbeqr_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the LeadBusinessExtraQueueRule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $lbeqr_id ID
     * @return LeadBusinessExtraQueueRule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($lbeqr_id)
    {
        if (($model = LeadBusinessExtraQueueRule::findOne(['lbeqr_id' => $lbeqr_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
