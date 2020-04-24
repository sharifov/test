<?php

namespace frontend\controllers;

use Yii;
use common\models\UserContactList;
use common\models\search\UserContactListSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserContactListController implements the CRUD actions for UserContactList model.
 */
class UserContactListController extends Controller
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
     * Lists all UserContactList models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserContactListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserContactList model.
     * @param integer $ucl_user_id
     * @param integer $ucl_client_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ucl_user_id, $ucl_client_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($ucl_user_id, $ucl_client_id),
        ]);
    }

    /**
     * Creates a new UserContactList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserContactList();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ucl_user_id' => $model->ucl_user_id, 'ucl_client_id' => $model->ucl_client_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserContactList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $ucl_user_id
     * @param integer $ucl_client_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($ucl_user_id, $ucl_client_id)
    {
        $model = $this->findModel($ucl_user_id, $ucl_client_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ucl_user_id' => $model->ucl_user_id, 'ucl_client_id' => $model->ucl_client_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserContactList model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $ucl_user_id
     * @param integer $ucl_client_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($ucl_user_id, $ucl_client_id)
    {
        $this->findModel($ucl_user_id, $ucl_client_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserContactList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $ucl_user_id
     * @param integer $ucl_client_id
     * @return UserContactList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ucl_user_id, $ucl_client_id)
    {
        if (($model = UserContactList::findOne(['ucl_user_id' => $ucl_user_id, 'ucl_client_id' => $ucl_client_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
