<?php

namespace frontend\controllers;

use src\auth\Auth;
use Yii;
use src\model\userData\entity\UserData;
use src\model\userData\entity\search\UserDataSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class UserDataCrudController extends FController
{
    public function behaviors(): array
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
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new UserDataSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $ud_user_id User ID
     * @param int $ud_key Key
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ud_user_id, $ud_key): string
    {
        return $this->render('view', [
            'model' => $this->findModel($ud_user_id, $ud_key),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new UserData();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->ud_updated_dt = date('Y-m-d H:i:s');
            $model->save(false);
            return $this->redirect(['view', 'ud_user_id' => $model->ud_user_id, 'ud_key' => $model->ud_key]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $ud_user_id User ID
     * @param int $ud_key Key
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($ud_user_id, $ud_key)
    {
        $model = $this->findModel($ud_user_id, $ud_key);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->ud_updated_dt = date('Y-m-d H:i:s');
            $model->save(false);
            return $this->redirect(['view', 'ud_user_id' => $model->ud_user_id, 'ud_key' => $model->ud_key]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $ud_user_id User ID
     * @param int $ud_key Key
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($ud_user_id, $ud_key): Response
    {
        $this->findModel($ud_user_id, $ud_key)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param int $ud_user_id User ID
     * @param int $ud_key Key
     * @return UserData
     * @throws NotFoundHttpException
     */
    protected function findModel($ud_user_id, $ud_key): UserData
    {
        if (($model = UserData::findOne(['ud_user_id' => $ud_user_id, 'ud_key' => $ud_key])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
