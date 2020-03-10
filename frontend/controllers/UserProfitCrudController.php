<?php

namespace frontend\controllers;

use common\models\Employee;
use Yii;
use sales\model\user\entity\profit\UserProfit;
use sales\model\user\entity\profit\search\UserProfitSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserProfitCrudController implements the CRUD actions for UserProfit model.
 */
class UserProfitCrudController extends FController
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
     * Lists all UserProfit models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserProfitSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserProfit model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new UserProfit model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserProfit();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->up_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserProfit model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->up_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

	/**
	 * @param $id
	 * @return \yii\web\Response
	 * @throws NotFoundHttpException
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

	/**
	 * @throws NotFoundHttpException
	 */
    public function actionGetAgentPercentCommission(): \yii\web\Response
	{
		if (Yii::$app->request->isPost) {
			$userId = Yii::$app->request->post('userId');
			$agentCommission = Employee::findOne(['id' => $userId]);
			return $this->asJson(['agentCommission' => $agentCommission->userParams->up_commission_percent ?? 0]);
		}
		throw new NotFoundHttpException('Page not found');
	}

    /**
     * Finds the UserProfit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserProfit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserProfit::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
