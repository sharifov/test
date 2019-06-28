<?php

namespace frontend\controllers;

use Yii;
use common\models\LeadChecklist;
use common\models\search\LeadChecklistSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LeadChecklistController implements the CRUD actions for LeadChecklist model.
 */
class LeadChecklistController extends FController
{
    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
                        'roles' => ['manageLeadChecklist']
                    ]
                ]
            ],
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
     * Lists all LeadChecklist models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LeadChecklistSearch();
        $params = Yii::$app->request->queryParams;
        if (isset($params['reset'])) {
            unset($params['LeadChecklistSearch']['date_range']);
        }

        $searchModel->datetime_start = date('Y-m-d', strtotime('-0 day'));
        $searchModel->datetime_end = date('Y-m-d');
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LeadChecklist model.
     * @param integer $lc_type_id
     * @param integer $lc_lead_id
     * @param integer $lc_user_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($lc_type_id, $lc_lead_id, $lc_user_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($lc_type_id, $lc_lead_id, $lc_user_id),
        ]);
    }

    /**
     * Creates a new LeadChecklist model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new LeadChecklist();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'lc_type_id' => $model->lc_type_id, 'lc_lead_id' => $model->lc_lead_id, 'lc_user_id' => $model->lc_user_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing LeadChecklist model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $lc_type_id
     * @param integer $lc_lead_id
     * @param integer $lc_user_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($lc_type_id, $lc_lead_id, $lc_user_id)
    {
        $model = $this->findModel($lc_type_id, $lc_lead_id, $lc_user_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'lc_type_id' => $model->lc_type_id, 'lc_lead_id' => $model->lc_lead_id, 'lc_user_id' => $model->lc_user_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing LeadChecklist model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $lc_type_id
     * @param integer $lc_lead_id
     * @param integer $lc_user_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($lc_type_id, $lc_lead_id, $lc_user_id)
    {
        $this->findModel($lc_type_id, $lc_lead_id, $lc_user_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the LeadChecklist model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $lc_type_id
     * @param integer $lc_lead_id
     * @param integer $lc_user_id
     * @return LeadChecklist the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($lc_type_id, $lc_lead_id, $lc_user_id)
    {
        if (($model = LeadChecklist::findOne(['lc_type_id' => $lc_type_id, 'lc_lead_id' => $lc_lead_id, 'lc_user_id' => $lc_user_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
