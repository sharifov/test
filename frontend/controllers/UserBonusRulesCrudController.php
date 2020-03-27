<?php

namespace frontend\controllers;

use Yii;
use common\models\UserBonusRules;
use common\models\search\UserBonusRulesSearch;
use frontend\controllers\FController;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserBonusRulesCrudController implements the CRUD actions for UserBonusRules model.
 */
class UserBonusRulesCrudController extends FController
{
    /**
     * {@inheritdoc}
     */
	public function behaviors()
	{
		$behaviors = [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['POST'],
				],
			],
		];
		return ArrayHelper::merge(parent::behaviors(), $behaviors);
	}

    /**
     * Lists all UserBonusRules models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserBonusRulesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserBonusRules model.
     * @param integer $ubr_exp_month
     * @param integer $ubr_kpi_percent
     * @param integer $ubr_order_profit
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ubr_exp_month, $ubr_kpi_percent, $ubr_order_profit)
    {
        return $this->render('view', [
            'model' => $this->findModel($ubr_exp_month, $ubr_kpi_percent, $ubr_order_profit),
        ]);
    }

    /**
     * Creates a new UserBonusRules model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserBonusRules();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ubr_exp_month' => $model->ubr_exp_month, 'ubr_kpi_percent' => $model->ubr_kpi_percent, 'ubr_order_profit' => $model->ubr_order_profit]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserBonusRules model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $ubr_exp_month
     * @param integer $ubr_kpi_percent
     * @param integer $ubr_order_profit
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($ubr_exp_month, $ubr_kpi_percent, $ubr_order_profit)
    {
        $model = $this->findModel($ubr_exp_month, $ubr_kpi_percent, $ubr_order_profit);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ubr_exp_month' => $model->ubr_exp_month, 'ubr_kpi_percent' => $model->ubr_kpi_percent, 'ubr_order_profit' => $model->ubr_order_profit]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserBonusRules model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $ubr_exp_month
     * @param integer $ubr_kpi_percent
     * @param integer $ubr_order_profit
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($ubr_exp_month, $ubr_kpi_percent, $ubr_order_profit)
    {
        $this->findModel($ubr_exp_month, $ubr_kpi_percent, $ubr_order_profit)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserBonusRules model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $ubr_exp_month
     * @param integer $ubr_kpi_percent
     * @param integer $ubr_order_profit
     * @return UserBonusRules the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ubr_exp_month, $ubr_kpi_percent, $ubr_order_profit)
    {
        if (($model = UserBonusRules::findOne(['ubr_exp_month' => $ubr_exp_month, 'ubr_kpi_percent' => $ubr_kpi_percent, 'ubr_order_profit' => $ubr_order_profit])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
