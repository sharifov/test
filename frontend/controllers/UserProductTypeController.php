<?php

namespace frontend\controllers;

use Yii;
use common\models\UserProductType;
use common\models\search\UserProductTypeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserProductTypeController implements the CRUD actions for UserProductType model.
 */
class UserProductTypeController extends Controller
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
     * Lists all UserProductType models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserProductTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserProductType model.
     * @param integer $upt_user_id
     * @param integer $upt_product_type_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($upt_user_id, $upt_product_type_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($upt_user_id, $upt_product_type_id),
        ]);
    }

    /**
     * Creates a new UserProductType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserProductType();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'upt_user_id' => $model->upt_user_id, 'upt_product_type_id' => $model->upt_product_type_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserProductType model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $upt_user_id
     * @param integer $upt_product_type_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($upt_user_id, $upt_product_type_id)
    {
        $model = $this->findModel($upt_user_id, $upt_product_type_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'upt_user_id' => $model->upt_user_id, 'upt_product_type_id' => $model->upt_product_type_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserProductType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $upt_user_id
     * @param integer $upt_product_type_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($upt_user_id, $upt_product_type_id)
    {
        $this->findModel($upt_user_id, $upt_product_type_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserProductType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $upt_user_id
     * @param integer $upt_product_type_id
     * @return UserProductType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($upt_user_id, $upt_product_type_id)
    {
        if (($model = UserProductType::findOne(['upt_user_id' => $upt_user_id, 'upt_product_type_id' => $upt_product_type_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
