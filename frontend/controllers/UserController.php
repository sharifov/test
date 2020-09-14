<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\Notifications;
use common\models\search\EmailSearch;
use common\models\search\SmsSearch;
use frontend\models\search\UserSiteActivitySearch;
use sales\auth\Auth;
use sales\model\callLog\entity\callLog\search\CallLogSearch;
use sales\model\clientChat\entity\search\ClientChatSearch;
use sales\model\user\entity\monitor\search\UserMonitorSearch;
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
 * UserController implements the CRUD actions for UserCallStatus model.
 */
class UserController extends FController
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
     * Lists all UserCallStatus models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserCallStatusSearch();
        $params = Yii::$app->request->queryParams;
        if(isset($params['reset'])){
            unset($params['UserCallStatusSearch']['date_range']);
        }

        $dataProvider = $searchModel->search($params);

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
     * @param $id
     * @return Employee|null
     * @throws NotFoundHttpException
     */
    protected function findUserModel(int $id): ?Employee
    {
        if (($model = Employee::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested user does not exist.');
    }

    /**
     * @return array
     */
    public function actionUpdateStatus(): array
    {
        $type_id = (int) Yii::$app->request->post('type_id');
        if ($type_id > 0) {
            $ucs = new UserCallStatus();
            $ucs->us_type_id = $type_id;
            $ucs->us_user_id = Yii::$app->user->id;
            $ucs->us_created_dt = date('Y-m-d H:i:s');
            if (!$ucs->save()) {
                Yii::error(VarDumper::dumpAsString($ucs->errors), 'UserCallStatusController:actionUpdateStatus:save');
            } else {
                // Notifications::socket($ucs->us_user_id, null, 'updateUserCallStatus', ['id' => 'ucs'.$ucs->us_id, 'type_id' => $type_id]);
                Notifications::publish('updateUserCallStatus', ['user_id' =>$ucs->us_user_id], ['id' => 'ucs'.$ucs->us_id, 'type_id' => $type_id]);

            }
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['type_id' => $type_id];
    }

    /**
     * Displays a single UserCallStatus model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionInfo($id)
    {
        $params = Yii::$app->request->queryParams;

        $searchModel = new UserMonitorSearch();
        $startDateTime = date('Y-m-d H:i', strtotime('-1 day'));
        $endDateTime = date('Y-m-d H:i', strtotime('+10 hours'));
        $data = $searchModel->searchStats(['UserMonitorSearch' => ['um_user_id' => $id]], $startDateTime);

        $userSiteActivityModel = new UserSiteActivitySearch();
        $userActivity = $userSiteActivityModel->searchReport(['UserSiteActivitySearch' => ['usa_user_id' => $id]]);

        $callLogSearchModel = new CallLogSearch();
        $callLogDataProvider = $callLogSearchModel->searchMyCalls($params, Employee::findIdentity($id));

        $emailSearchModel = new EmailSearch();
        $params['EmailSearch']['e_created_user_id'] = $id;
        $emailDataProvider = $emailSearchModel->search($params);
        $emailDataProvider->pagination->pageSize = 10;

        $smsSearchModel = new SmsSearch();
        $params['SmsSearch']['s_created_user_id'] = $id;
        $smsDataProvider = $smsSearchModel->search($params);
        $smsDataProvider->pagination->pageSize = 10;

        $chatSearchModel = new ClientChatSearch();
        $userTimezone = Auth::user()->userParams->up_timezone ?? 'UTC';
        $currentDate = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->setTimezone(new \DateTimeZone($userTimezone));
        $chatSearchModel->timeStart = ($currentDate->modify($chatSearchModel::DEFAULT_INTERVAL_BETWEEN_DAYS))->format('Y-m-d') . ' 00:00:00';
        $chatSearchModel->timeEnd = $currentDate->format('Y-m-d') . ' 23:59:59';
        $chatSearchModel->timeRange = $chatSearchModel->timeStart . ' - ' . $chatSearchModel->timeEnd;
        $params['ClientChatSearch']['cch_owner_user_id'] = $id;
        $chatDataProvider = $chatSearchModel->search($params);
        $chatDataProvider->pagination->pageSize = 10;



        return $this->render('info', [
            'model' => $this->findUserModel($id),
            'data' => $data,
            'startDateTime' => $startDateTime,
            'endDateTime' => $endDateTime,
            'userActivity' => $userActivity,
            'callLogDataProvider' => $callLogDataProvider,
            'callLogSearchModel' => $callLogSearchModel,
            'emailDataProvider' => $emailDataProvider,
            'emailSearchModel' => $emailSearchModel,
            'smsDataProvider' => $smsDataProvider,
            'smsSearchModel' => $smsSearchModel,
            'chatDataProvider' => $chatDataProvider,
            'chatSearchModel' => $chatSearchModel,
        ]);
    }
}
