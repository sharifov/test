<?php

namespace frontend\controllers;

use frontend\widgets\multipleUpdate\myNotifications\MultipleUpdateForm;
use frontend\widgets\multipleUpdate\myNotifications\MultipleUpdateService;
use frontend\widgets\notification\NotificationCache;
use frontend\widgets\notification\NotificationMessage;
use frontend\widgets\notification\NotificationWidget;
use modules\notification\src\abac\dto\NotificationAbacDto;
use modules\notification\src\abac\NotificationAbacObject;
use src\auth\Auth;
use Yii;
use common\models\Notifications;
use common\models\search\NotificationsSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * NotificationsController implements the CRUD actions for Notifications model.
 *
 * @property-read MultipleUpdateService $multipleUpdateService
 */
class NotificationsController extends FController
{
    private MultipleUpdateService $multipleUpdateService;

    public function __construct($id, $module, MultipleUpdateService $multipleUpdateService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->multipleUpdateService = $multipleUpdateService;
    }

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
                'only' => ['multiple-update-read'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['multiple-update-read'],
                    ],
                ],
            ]
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

    public function actionMultipleUpdateRead()
    {
        $notificationAbacDto = new NotificationAbacDto(null);
        /** @abac $abacDto, NotificationAbacDto::OBJ_PREVIEW_EMAIL, EmailAbacObject::ACTION_MULTIPLE_UPDATE_MAKE_READ, Access to action multiple update notification */
        if (!Yii::$app->abac->can($notificationAbacDto, NotificationAbacObject::OBJ_NOTIFICATION_MULTIPLE_UPDATE, NotificationAbacObject::ACTION_MULTIPLE_UPDATE_MAKE_READ, Auth::user())) {
            throw new ForbiddenHttpException('Access Denied');
        }

        $result = [
            'error' => false,
            'message' => 'Notification updated successfully'
        ];

        $form = new MultipleUpdateForm();
        $form->load(Yii::$app->request->post());
        if ($form->validate()) {
            $this->multipleUpdateService->makeReadNotifications($form->ids, Auth::id());
            return $this->asJson($result);
        }
        $result['error'] = true;
        $result['message'] = $form->getErrorSummary(true)[0] ?? '';
        return $this->asJson($result);
    }

    /**
     * Lists all Notifications models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NotificationsSearch();
        $params = Yii::$app->request->queryParams;
        if (isset($params['reset'])) {
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
        if ($model->n_user_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('Access denied.');
        }

        if (!$model->n_read_dt) {
            $model->n_read_dt = date('Y-m-d H:i:s');
            $model->n_new = false;
            if ($model->save()) {
                $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::delete($model) : [];
                Notifications::publish('getNewNotification', ['user_id' => $model->n_user_id], $dataNotification);
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view-ajax', [
                'model' => $model,
            ]);
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
            $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($model) : [];
            Notifications::publish('getNewNotification', ['user_id' => $model->n_user_id], $dataNotification);
            return $this->redirect(['view', 'id' => $model->n_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
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
            return $this->redirect(['view', 'id' => $model->n_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
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
            $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::delete($model) : [];
            Notifications::publish('getNewNotification', ['user_id' => $model->n_user_id], $dataNotification);
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
            $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::delete($model) : [];
            Notifications::publish('getNewNotification', ['user_id' => $model->n_user_id], $dataNotification);
        }
        return $this->redirect(['list']);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionAllDelete()
    {
        $userId = Auth::id();
        if (Notifications::updateAll(['n_deleted' => true], ['n_deleted' => false, 'n_user_id' => $userId])) {
            NotificationCache::invalidate($userId);
            $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::deleteAll($userId) : [];
            Notifications::publish('getNewNotification', ['user_id' => $userId], $dataNotification);
        }
        return $this->redirect(['list']);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionAllRead()
    {
        $userId = Auth::id();
        if (Notifications::updateAll(['n_new' => false, 'n_read_dt' => date('Y-m-d H:i:s')], ['n_read_dt' => null, 'n_user_id' => $userId])) {
            NotificationCache::invalidate($userId);
            $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::deleteAll($userId) : [];
            Notifications::publish('getNewNotification', ['user_id' => $userId], $dataNotification);
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
        if (Yii::$app->request->isAjax) {
//            $box = \frontend\widgets\Notifications::getInstance();
            return (new NotificationWidget(['userId' => Auth::id()]))->run();
        } elseif (Yii::$app->request->referrer) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        return $this->redirect(['/site/profile']);
    }
}
