<?php

namespace frontend\controllers;

use Yii;
use common\models\UserGroupAssign;
use common\models\search\UserGroupAssignSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserGroupAssignController implements the CRUD actions for UserGroupAssign model.
 */
class UserGroupAssignController extends FController
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
                        'actions' => ['index', 'update', 'delete', 'create', 'view'],
                        'allow' => true,
                        'roles' => ['admin','userManager'], //'supervision',
                    ],
                    /*[
                        'actions' => ['view', 'index'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],*/
                ],
            ],
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all UserGroupAssign models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserGroupAssignSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserGroupAssign model.
     * @param integer $ugs_user_id
     * @param integer $ugs_group_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ugs_user_id, $ugs_group_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($ugs_user_id, $ugs_group_id),
        ]);
    }

    /**
     * Creates a new UserGroupAssign model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserGroupAssign();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ugs_user_id' => $model->ugs_user_id, 'ugs_group_id' => $model->ugs_group_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserGroupAssign model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $ugs_user_id
     * @param integer $ugs_group_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($ugs_user_id, $ugs_group_id)
    {
        $model = $this->findModel($ugs_user_id, $ugs_group_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ugs_user_id' => $model->ugs_user_id, 'ugs_group_id' => $model->ugs_group_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserGroupAssign model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $ugs_user_id
     * @param integer $ugs_group_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($ugs_user_id, $ugs_group_id)
    {
        $this->findModel($ugs_user_id, $ugs_group_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserGroupAssign model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $ugs_user_id
     * @param integer $ugs_group_id
     * @return UserGroupAssign the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ugs_user_id, $ugs_group_id)
    {
        if (($model = UserGroupAssign::findOne(['ugs_user_id' => $ugs_user_id, 'ugs_group_id' => $ugs_group_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
