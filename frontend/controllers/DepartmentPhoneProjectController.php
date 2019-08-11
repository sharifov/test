<?php

namespace frontend\controllers;

use Yii;
use common\models\DepartmentPhoneProject;
use common\models\search\DepartmentPhoneProjectSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DepartmentPhoneProjectController implements the CRUD actions for DepartmentPhoneProject model.
 */
class DepartmentPhoneProjectController extends FController
{

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
     * Lists all DepartmentPhoneProject models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DepartmentPhoneProjectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DepartmentPhoneProject model.
     * @param integer $dpp_dep_id
     * @param integer $dpp_project_id
     * @param string $dpp_phone_number
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($dpp_dep_id, $dpp_project_id, $dpp_phone_number)
    {
        return $this->render('view', [
            'model' => $this->findModel($dpp_dep_id, $dpp_project_id, $dpp_phone_number),
        ]);
    }

    /**
     * Creates a new DepartmentPhoneProject model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DepartmentPhoneProject();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'dpp_dep_id' => $model->dpp_dep_id, 'dpp_project_id' => $model->dpp_project_id, 'dpp_phone_number' => $model->dpp_phone_number]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DepartmentPhoneProject model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $dpp_dep_id
     * @param integer $dpp_project_id
     * @param string $dpp_phone_number
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($dpp_dep_id, $dpp_project_id, $dpp_phone_number)
    {
        $model = $this->findModel($dpp_dep_id, $dpp_project_id, $dpp_phone_number);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'dpp_dep_id' => $model->dpp_dep_id, 'dpp_project_id' => $model->dpp_project_id, 'dpp_phone_number' => $model->dpp_phone_number]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DepartmentPhoneProject model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $dpp_dep_id
     * @param integer $dpp_project_id
     * @param string $dpp_phone_number
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($dpp_dep_id, $dpp_project_id, $dpp_phone_number)
    {
        $this->findModel($dpp_dep_id, $dpp_project_id, $dpp_phone_number)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the DepartmentPhoneProject model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $dpp_dep_id
     * @param integer $dpp_project_id
     * @param string $dpp_phone_number
     * @return DepartmentPhoneProject the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($dpp_dep_id, $dpp_project_id, $dpp_phone_number)
    {
        if (($model = DepartmentPhoneProject::findOne(['dpp_dep_id' => $dpp_dep_id, 'dpp_project_id' => $dpp_project_id, 'dpp_phone_number' => $dpp_phone_number])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
