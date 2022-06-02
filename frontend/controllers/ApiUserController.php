<?php

namespace frontend\controllers;

use Yii;
use common\models\ApiUser;
use common\models\search\ApiUserSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ApiUserController implements the CRUD actions for ApiUser model.
 */
class ApiUserController extends FController
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

    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    /**
     * Lists all ApiUser models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ApiUserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ApiUser model.
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
     * Creates a new ApiUser model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ApiUser();
        $model->scenario = 'insert';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->setPassword($model->au_api_password);
            $model->au_updated_dt = date('Y-m-d H:i:s');
            $model->au_updated_user_id = Yii::$app->user->id;

            if ($model->save(false)) {
                return $this->redirect(['view', 'id' => $model->au_id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ApiUser model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (empty($model->au_api_password)) {
                unset($model->au_api_password);
            } else {
                $model->setPassword($model->au_api_password);
            }

                $model->au_updated_dt = date('Y-m-d H:i:s');
                $model->au_updated_user_id = Yii::$app->user->id;

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->au_id]);
            }
        } else {
            $model->au_api_password = '';
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
     * Finds the ApiUser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ApiUser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ApiUser::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
