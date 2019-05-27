<?php

namespace frontend\controllers;

use common\models\Notifications;
use Yii;
use common\models\UserCallStatus;
use common\models\search\UserCallStatusSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserCallStatusController implements the CRUD actions for UserCallStatus model.
 */
class UserCallStatusController extends FController
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
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create', 'update-status'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'actions' => ['index', 'update-status'],
                        'allow' => true,
                        'roles' => ['agent', 'supervision'],
                    ],
                ],
            ],
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all UserCallStatus models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserCallStatusSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserCallStatus model.
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
     * Creates a new UserCallStatus model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserCallStatus();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->us_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserCallStatus model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->us_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserCallStatus model.
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
     * Finds the UserCallStatus model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserCallStatus the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserCallStatus::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @return array
     */
    public function actionUpdateStatus(): array
    {
        $type_id = (int) Yii::$app->request->post('type_id');
        if($type_id > 0) {
            $ucs = new UserCallStatus();
            $ucs->us_type_id = $type_id;
            $ucs->us_user_id = Yii::$app->user->id;
            $ucs->us_created_dt = date('Y-m-d H:i:s');
            if(!$ucs->save()) {
                Yii::error(VarDumper::dumpAsString($ucs->errors), 'UserCallStatusController:actionUpdateStatus:save');
            } else {
                Notifications::socket($ucs->us_user_id, null, 'updateUserCallStatus', ['type_id' => $type_id]);
            }
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['type_id' => $type_id];
    }
}
