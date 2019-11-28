<?php

namespace frontend\controllers;

use common\models\DepartmentEmailProjectUserGroup;
use Yii;
use common\models\DepartmentEmailProject;
use common\models\search\DepartmentEmailProjectSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DepartmentEmailProjectController implements the CRUD actions for DepartmentEmailProject model.
 */
class DepartmentEmailProjectController extends FController
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
     * Lists all DepartmentEmailProject models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DepartmentEmailProjectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DepartmentEmailProject model.
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
     * Creates a new DepartmentEmailProject model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DepartmentEmailProject();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			if ($model->user_group_list) {
				foreach ($model->user_group_list as $userGroupId) {
					$dug = new DepartmentEmailProjectUserGroup();
					$dug->dug_ug_id = $userGroupId;
					$dug->link('dugDep', $model);
				}
			}

            return $this->redirect(['view', 'id' => $model->dep_id]);
        }

		$model->user_group_list = [];

		return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DepartmentEmailProject model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

			if ($model->user_group_list) {

				DepartmentEmailProjectUserGroup::deleteAll(['dug_dep_id' => $model->dep_id]);

				foreach ($model->user_group_list as $userGroupId) {
					$dug = new DepartmentEmailProjectUserGroup();
					$dug->dug_ug_id = $userGroupId;
					$dug->link('dugDep', $model);
				}
			}

            return $this->redirect(['view', 'id' => $model->dep_id]);
        }

		$model->user_group_list = ArrayHelper::map($model->dugUgs, 'ug_id', 'ug_id');

		return $this->render('update', [
            'model' => $model,
        ]);
    }

	/**
	 * Deletes an existing DepartmentEmailProject model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the DepartmentEmailProject model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DepartmentEmailProject the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DepartmentEmailProject::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
