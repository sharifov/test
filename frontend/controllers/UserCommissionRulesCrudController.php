<?php

namespace frontend\controllers;

use Yii;
use common\models\UserCommissionRules;
use common\models\search\UserCommissionRulesSearch;
use frontend\controllers\FController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserCommissionRulesCrudController implements the CRUD actions for UserCommissionRules model.
 */
class UserCommissionRulesCrudController extends FController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all UserCommissionRules models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserCommissionRulesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserCommissionRules model.
     * @param integer $ucr_exp_month
     * @param integer $ucr_kpi_percent
     * @param integer $ucr_order_profit
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ucr_exp_month, $ucr_kpi_percent, $ucr_order_profit)
    {
        return $this->render('view', [
            'model' => $this->findModel($ucr_exp_month, $ucr_kpi_percent, $ucr_order_profit),
        ]);
    }

    /**
     * Creates a new UserCommissionRules model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserCommissionRules();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ucr_exp_month' => $model->ucr_exp_month, 'ucr_kpi_percent' => $model->ucr_kpi_percent, 'ucr_order_profit' => $model->ucr_order_profit]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserCommissionRules model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $ucr_exp_month
     * @param integer $ucr_kpi_percent
     * @param integer $ucr_order_profit
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($ucr_exp_month, $ucr_kpi_percent, $ucr_order_profit)
    {
        $model = $this->findModel($ucr_exp_month, $ucr_kpi_percent, $ucr_order_profit);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ucr_exp_month' => $model->ucr_exp_month, 'ucr_kpi_percent' => $model->ucr_kpi_percent, 'ucr_order_profit' => $model->ucr_order_profit]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserCommissionRules model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $ucr_exp_month
     * @param integer $ucr_kpi_percent
     * @param integer $ucr_order_profit
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($ucr_exp_month, $ucr_kpi_percent, $ucr_order_profit)
    {
        $this->findModel($ucr_exp_month, $ucr_kpi_percent, $ucr_order_profit)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserCommissionRules model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $ucr_exp_month
     * @param integer $ucr_kpi_percent
     * @param integer $ucr_order_profit
     * @return UserCommissionRules the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ucr_exp_month, $ucr_kpi_percent, $ucr_order_profit)
    {
        if (($model = UserCommissionRules::findOne(['ucr_exp_month' => $ucr_exp_month, 'ucr_kpi_percent' => $ucr_kpi_percent, 'ucr_order_profit' => $ucr_order_profit])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
