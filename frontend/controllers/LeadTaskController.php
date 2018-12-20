<?php

namespace frontend\controllers;

use Yii;
use common\models\LeadTask;
use common\models\search\LeadTaskSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LeadTaskController implements the CRUD actions for LeadTask model.
 */
class LeadTaskController extends FController
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
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin'],
                    ],
                    [
                        'actions' => ['view', 'index'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all LeadTask models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LeadTaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LeadTask model.
     * @param integer $lt_lead_id
     * @param integer $lt_task_id
     * @param integer $lt_user_id
     * @param string $lt_date
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($lt_lead_id, $lt_task_id, $lt_user_id, $lt_date)
    {
        return $this->render('view', [
            'model' => $this->findModel($lt_lead_id, $lt_task_id, $lt_user_id, $lt_date),
        ]);
    }

    /**
     * Creates a new LeadTask model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new LeadTask();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'lt_lead_id' => $model->lt_lead_id, 'lt_task_id' => $model->lt_task_id, 'lt_user_id' => $model->lt_user_id, 'lt_date' => $model->lt_date]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing LeadTask model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $lt_lead_id
     * @param integer $lt_task_id
     * @param integer $lt_user_id
     * @param string $lt_date
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($lt_lead_id, $lt_task_id, $lt_user_id, $lt_date)
    {
        $model = $this->findModel($lt_lead_id, $lt_task_id, $lt_user_id, $lt_date);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'lt_lead_id' => $model->lt_lead_id, 'lt_task_id' => $model->lt_task_id, 'lt_user_id' => $model->lt_user_id, 'lt_date' => $model->lt_date]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing LeadTask model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $lt_lead_id
     * @param integer $lt_task_id
     * @param integer $lt_user_id
     * @param string $lt_date
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($lt_lead_id, $lt_task_id, $lt_user_id, $lt_date)
    {
        $this->findModel($lt_lead_id, $lt_task_id, $lt_user_id, $lt_date)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the LeadTask model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $lt_lead_id
     * @param integer $lt_task_id
     * @param integer $lt_user_id
     * @param string $lt_date
     * @return LeadTask the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($lt_lead_id, $lt_task_id, $lt_user_id, $lt_date)
    {
        if (($model = LeadTask::findOne(['lt_lead_id' => $lt_lead_id, 'lt_task_id' => $lt_task_id, 'lt_user_id' => $lt_user_id, 'lt_date' => $lt_date])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
