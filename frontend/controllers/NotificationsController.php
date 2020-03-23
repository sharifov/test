<?php

namespace frontend\controllers;

use frontend\widgets\notification\NotificationCache;
use frontend\widgets\notification\NotificationWidget;
use sales\auth\Auth;
use Yii;
use common\models\Notifications;
use common\models\search\NotificationsSearch;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * NotificationsController implements the CRUD actions for Notifications model.
 */
class NotificationsController extends FController
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
     * Lists all Notifications models.
     * @return mixed
     */
    public function actionList()
    {
        $searchModel = new NotificationsSearch();

        $params = Yii::$app->request->queryParams;
        $params['NotificationsSearch']['n_deleted'] = 0;
        $params['NotificationsSearch']['n_user_id'] = Yii::$app->user->id;


        $dataProvider = $searchModel->search($params);

        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Notifications models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NotificationsSearch();
        $params = Yii::$app->request->queryParams;
        if(isset($params['reset'])){
            unset($params['NotificationsSearch']['date_range']);
        }

        $searchModel->datetime_start = date('Y-m-d', strtotime('-0 day'));
        $searchModel->datetime_end = date('Y-m-d');
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


    /**
     * @param $id
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionView2($id)
    {
        $model = $this->findModel($id);
        if($model->n_user_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('Access denied.');
        }

        if(!$model->n_read_dt) {
            $model->n_read_dt = date('Y-m-d H:i:s');
            $model->n_new = false;
            if ($model->save()) {
                Notifications::sendSocket('getNewNotification', ['user_id' => $model->n_user_id]);
            }
        }

        return $this->render('view2', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Notifications model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Notifications();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //Notifications::socket($model->n_user_id, null, 'getNewNotification', [], true);
            Notifications::sendSocket('getNewNotification', ['user_id' => $model->n_user_id]);
            return $this->redirect(['view', 'id' => $model->n_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Notifications model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Notifications::sendSocket('getNewNotification', ['user_id' => $model->n_user_id]);
            return $this->redirect(['view', 'id' => $model->n_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Notifications model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->delete()) {
            NotificationCache::invalidate($model->n_user_id);
            Notifications::sendSocket('getNewNotification', ['user_id' => $model->n_user_id]);
        }

        return $this->redirect(['index']);
    }


    /**
     * @param $id
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     */
    public function actionSoftDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->n_user_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('Access denied.');
        }

        $model->n_deleted = true;
        if ($model->save()) {
            Notifications::sendSocket('getNewNotification', ['user_id' => $model->n_user_id]);
        }
        return $this->redirect(['list']);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionAllDelete()
    {
        if (Notifications::updateAll(['n_deleted' => true], ['n_deleted' => false, 'n_user_id' => Yii::$app->user->id])) {
            NotificationCache::invalidate(Yii::$app->user->id);
            Notifications::sendSocket('getNewNotification', ['user_id' => Yii::$app->user->id]);
        }
        return $this->redirect(['list']);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionAllRead()
    {
        if (Notifications::updateAll(['n_new' => false, 'n_read_dt' => date('Y-m-d H:i:s')], ['n_read_dt' => null, 'n_user_id' => Yii::$app->user->id])) {
            NotificationCache::invalidate(Yii::$app->user->id);
            Notifications::sendSocket('getNewNotification', ['user_id' => Yii::$app->user->id]);
        }
        return $this->redirect(['list']);
    }

    /**
     * Finds the Notifications model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Notifications the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Notifications::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionPjaxNotify()
    {
        if(Yii::$app->request->isAjax) {
//            $box = \frontend\widgets\Notifications::getInstance();
            return (new NotificationWidget(['userId' => Auth::id()]))->run();
        } elseif(Yii::$app->request->referrer) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        return $this->redirect(['site/profile']);
    }
}
