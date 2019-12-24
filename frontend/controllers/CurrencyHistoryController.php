<?php

namespace frontend\controllers;

use Yii;
use common\models\CurrencyHistory;
use common\models\search\CurrencyHistorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CurrencyHistoryController implements the CRUD actions for CurrencyHistory model.
 */
class CurrencyHistoryController extends Controller
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
     * @param string $cur_his_code
     * @param string $cur_his_created
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($cur_his_code, $cur_his_created)
    {
        return $this->render('view', [
            'model' => $this->findModel($cur_his_code, $cur_his_created),
        ]);
    }

    /**
     * Finds the CurrencyHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $cur_his_code
     * @param string $cur_his_created
     * @return CurrencyHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($cur_his_code, $cur_his_created)
    {
        if (($model = CurrencyHistory::findOne(['cur_his_code' => $cur_his_code, 'cur_his_created' => $cur_his_created])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
