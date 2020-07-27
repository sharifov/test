<?php

namespace frontend\controllers;

use common\models\Employee;
use sales\helpers\app\AppHelper;
use Yii;
use frontend\models\UserFailedLogin;
use frontend\models\search\UserFailedLoginSearch;
use frontend\controllers\FController;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * UserFailedLoginController implements the CRUD actions for UserFailedLogin model.
 */
class UserFailedLoginController extends FController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all UserFailedLogin models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserFailedLoginSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserFailedLogin model.
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
     * Creates a new UserFailedLogin model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserFailedLogin();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ufl_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserFailedLogin model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ufl_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserFailedLogin model.
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
     * @return array
     */
    public function actionSetActiveAjax(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['message' => '', 'status' => 0];

        if (Yii::$app->request->isAjax) {
            try {
                $userId = (int) Yii::$app->request->post('id');

                if ($user = Employee::findOne(['id' => $userId, 'status' => Employee::STATUS_BLOCKED])) {
                    $user->setActive();
                    if ($user->save(false)) {
                        $result['status'] = 1;
                        $result['message'] = 'Status changed to active.';
                    }  else {
                        throw new \DomainException($user->getErrorSummary(false)[0]);
                    }
                } else {
                    throw new \DomainException('Blocked user not found');
                }
            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableFormatter($throwable), 'UserFailedLoginController:actionSetActiveAjax:save');
                $result['message'] = VarDumper::dumpAsString($throwable->getMessage());
            }
        }
        return $result;
    }

    /**
     * Finds the UserFailedLogin model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserFailedLogin the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserFailedLogin::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
