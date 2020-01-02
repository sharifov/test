<?php

namespace frontend\controllers;

use Yii;
use common\models\CurrencyHistory;
use common\models\search\CurrencyHistorySearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CurrencyHistoryController implements the CRUD actions for CurrencyHistory model.
 */
class CurrencyHistoryController extends FController
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
     * Lists all CurrencyHistory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CurrencyHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CurrencyHistory model.
     * @param string $ch_code
     * @param string $ch_created_date
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ch_code, $ch_created_date)
    {
        return $this->render('view', [
            'model' => $this->findModel($ch_code, $ch_created_date),
        ]);
    }

    /**
     * Creates a new CurrencyHistory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CurrencyHistory();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ch_code' => $model->ch_code, 'ch_created_date' => $model->ch_created_date]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CurrencyHistory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $ch_code
     * @param string $ch_created_date
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($ch_code, $ch_created_date)
    {
        $model = $this->findModel($ch_code, $ch_created_date);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ch_code' => $model->ch_code, 'ch_created_date' => $model->ch_created_date]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

	/**
	 * Deletes an existing CurrencyHistory model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param string $ch_code
	 * @param string $ch_created_date
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
    public function actionDelete($ch_code, $ch_created_date)
    {
        $this->findModel($ch_code, $ch_created_date)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CurrencyHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $ch_code
     * @param string $ch_created_date
     * @return CurrencyHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ch_code, $ch_created_date)
    {
        if (($model = CurrencyHistory::findOne(['ch_code' => $ch_code, 'ch_created_date' => $ch_created_date])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
