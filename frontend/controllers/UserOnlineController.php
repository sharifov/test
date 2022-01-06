<?php

namespace frontend\controllers;

use AdvancedJsonRpc\Notification;
use common\models\Employee;
use common\models\Notifications;
use common\models\UserConnection;
use Yii;
use common\models\UserOnline;
use common\models\search\UserOnlineSearch;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * UserOnlineController implements the CRUD actions for UserOnline model.
 */
class UserOnlineController extends FController
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

    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    public function actionLogout()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $userId = (int)Yii::$app->request->post('userId');

        if (!$userId) {
            return [
                'error' => true,
                'message' => 'User ID is empty',
            ];
        }

        if (!UserOnline::find()->andWhere(['uo_user_id' => $userId])->one()) {
            return [
                'error' => true,
                'message' => 'User is already offline',
            ];
        }

        $pubChannel = UserConnection::getUserChannel($userId);
        Notifications::pub([$pubChannel], 'forceLogout', []);

        return [
            'error' => false,
        ];
    }

    public function actionRefresh()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $userId = (int)Yii::$app->request->post('userId');

        if (!$userId) {
            return [
                'error' => true,
                'message' => 'User ID is empty',
            ];
        }

        if (!UserOnline::find()->andWhere(['uo_user_id' => $userId])->one()) {
            return [
                'error' => true,
                'message' => 'User is offline',
            ];
        }

        $pubChannel = UserConnection::getUserChannel($userId);
        Notifications::pub([$pubChannel], 'forceRefresh', []);

        return [
            'error' => false,
        ];
    }

    /**
     * Lists all UserOnline models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserOnlineSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserOnline model.
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
     * Creates a new UserOnline model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserOnline();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->uo_user_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserOnline model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->uo_user_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserOnline model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserOnline model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserOnline the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserOnline::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
