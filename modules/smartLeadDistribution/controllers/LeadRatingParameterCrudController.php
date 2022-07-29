<?php

namespace modules\smartLeadDistribution\controllers;

use frontend\controllers\FController;
use modules\smartLeadDistribution\src\entities\LeadRatingParameter;
use modules\smartLeadDistribution\src\entities\LeadRatingParameterSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LeadRatingParameterCrudController implements the CRUD actions for LeadRatingParameter model.
 */
class LeadRatingParameterCrudController extends FController
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
     * Lists all LeadRatingParameter models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LeadRatingParameterSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LeadRatingParameter model.
     * @param int $lrp_id Lrp ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($lrp_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($lrp_id),
        ]);
    }

    /**
     * Creates a new LeadRatingParameter model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new LeadRatingParameter();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'lrp_id' => $model->lrp_id]);
            }
        } else {
            $model->loadDefaultValues();

            if (\Yii::$app->request->get('object')) {
                $model->lrp_object = \Yii::$app->request->get('object');
            }

            if (\Yii::$app->request->get('attribute')) {
                $model->lrp_attribute = \Yii::$app->request->get('attribute');
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing LeadRatingParameter model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $lrp_id Lrp ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($lrp_id)
    {
        $model = $this->findModel($lrp_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'lrp_id' => $model->lrp_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing LeadRatingParameter model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $lrp_id Lrp ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($lrp_id)
    {
        $this->findModel($lrp_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the LeadRatingParameter model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $lrp_id Lrp ID
     * @return LeadRatingParameter the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($lrp_id)
    {
        if (($model = LeadRatingParameter::findOne(['lrp_id' => $lrp_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
