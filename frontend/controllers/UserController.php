<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\Notifications;
use common\models\search\EmailSearch;
use common\models\search\LeadSearch;
use common\models\search\SmsSearch;
use modules\requestControl\models\search\UserSiteActivitySearch;
use src\model\userData\entity\search\UserDataSearch;
use src\auth\Auth;
use src\entities\cases\CasesSearch;
use src\model\callLog\entity\callLog\search\CallLogSearch;
use src\model\clientChat\entity\search\ClientChatSearch;
use src\model\user\entity\monitor\search\UserMonitorSearch;
use Yii;
use common\models\UserCallStatus;
use common\models\search\UserCallStatusSearch;
use yii\base\DynamicModel;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use modules\featureFlag\FFlag;

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

    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    /**
     * Lists all UserCallStatus models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserCallStatusSearch();
        $params = Yii::$app->request->queryParams;
        if (isset($params['reset'])) {
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
                Notifications::publish('updateUserCallStatus', ['user_id' => $ucs->us_user_id], ['id' => 'ucs' . $ucs->us_id, 'type_id' => $type_id]);
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
    public function actionInfo(int $id)
    {
        $params = Yii::$app->request->queryParams;

        $userTimezone = Auth::user()->userParams->up_timezone ?? 'UTC';
        $currentDate = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->setTimezone(new \DateTimeZone($userTimezone));

        $datePickerModel = new DynamicModel([
            'dateRange', 'dateStart', 'dateEnd'
        ]);
        $datePickerModel->addRule('dateStart', 'string');
        $datePickerModel->addRule('dateEnd', 'string');
        $datePickerModel->addRule('dateRange', 'match', ['pattern' => '/^.+\s\-\s.+$/']);

        if (!$datePickerModel->load(Yii::$app->request->get())) {
            $datePickerModel->dateStart = ($currentDate->modify('-6 days'))->format('Y-m-d') . ' 00:00:00';
            $datePickerModel->dateEnd = $currentDate->format('Y-m-d') . ' 23:59:59';
            $datePickerModel->dateRange = $datePickerModel->dateStart . ' - ' . $datePickerModel->dateEnd;
        }

        /*$searchModel = new UserMonitorSearch();
        $startDateTime = date('Y-m-d H:i', strtotime('-1 day'));
        $endDateTime = date('Y-m-d H:i', strtotime('+10 hours'));
        $data = $searchModel->searchStats(['UserMonitorSearch' => ['um_user_id' => $id]], $startDateTime);*/
        $userDataModel = new UserDataSearch();
        $userDataModel->ud_user_id = $id;
        $userDataProvider = $userDataModel->search($params, Auth::user());


        $userSiteActivityModel = new UserSiteActivitySearch();
        $userSiteActivityModel->createTimeStart = strtotime($datePickerModel->dateStart);
        $userSiteActivityModel->createTimeEnd = strtotime($datePickerModel->dateEnd);
        $userActivity = $userSiteActivityModel->searchReport(['UserSiteActivitySearch' => ['usa_user_id' => $id]]);

        $callLogSearchModel = new CallLogSearch();
        $callLogSearchModel->createTimeStart = $datePickerModel->dateStart;
        $callLogSearchModel->createTimeEnd = $datePickerModel->dateEnd;
        $callLogDataProvider = $callLogSearchModel->searchMyCalls($params, $id);

        $callsInfoGraph = $callLogSearchModel->searchCallsGraph($params, $id);

        $emailSearchModel = \Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_EMAIL_NORMALIZED_FORM_ENABLE) ? new \src\entities\email\EmailSearch() : new EmailSearch();
        $emailSearchModel->datetime_start = $datePickerModel->dateStart;
        $emailSearchModel->datetime_end = $datePickerModel->dateEnd;
        $params['EmailSearch']['e_created_user_id'] = $id;
        $emailDataProvider = $emailSearchModel->search($params);
        $emailDataProvider->pagination->pageSize = 10;

        $emailsInfoGraph = $emailSearchModel->searchEmailGraph($params, $id);

        $smsSearchModel = new SmsSearch();
        $smsSearchModel->datetime_start = $datePickerModel->dateStart;
        $smsSearchModel->datetime_end = $datePickerModel->dateEnd;
        $params['SmsSearch']['s_created_user_id'] = $id;
        $smsDataProvider = $smsSearchModel->search($params);
        $smsDataProvider->pagination->pageSize = 10;

        $smsInfoGraph = $smsSearchModel->searchSmsGraph($params, $id);

        $chatSearchModel = new ClientChatSearch();
        $chatSearchModel->timeStart = $datePickerModel->dateStart;
        $chatSearchModel->timeEnd = $datePickerModel->dateEnd;
        $params['ClientChatSearch']['cch_owner_user_id'] = $id;
        $chatDataProvider = $chatSearchModel->search($params);
        $chatDataProvider->pagination->pageSize = 10;

        $chatInfoGraph = $chatSearchModel->searchChatGraph($params, $id);

        $leadsSearchModel = new LeadSearch();
        $leadsSearchModel->datetime_start = $datePickerModel->dateStart;
        $leadsSearchModel->datetime_end = $datePickerModel->dateEnd;
        $leadsInfoDataProvider = $leadsSearchModel->searchUserLeadsInfo($params, $id);

        $casesSearchModel = new CasesSearch();
        $casesSearchModel->datetime_start =  $datePickerModel->dateStart;
        $casesSearchModel->datetime_end = $datePickerModel->dateEnd;

        $casesInfoDataProvider = $casesSearchModel->searchUserCasesInfo($params, $id);


        return $this->render('info', [
            'model' => $this->findUserModel($id),
            /*'data' => $data,
            'startDateTime' => $startDateTime,
            'endDateTime' => $endDateTime,*/
            'userDataProvider' => $userDataProvider,
            'userActivity' => $userActivity,
            'callLogDataProvider' => $callLogDataProvider,
            'callLogSearchModel' => $callLogSearchModel,
            'emailDataProvider' => $emailDataProvider,
            'emailSearchModel' => $emailSearchModel,
            'smsDataProvider' => $smsDataProvider,
            'smsSearchModel' => $smsSearchModel,
            'chatDataProvider' => $chatDataProvider,
            'chatSearchModel' => $chatSearchModel,
            'callsInfoGraph' => $callsInfoGraph,
            'emailsInfoGraph' => $emailsInfoGraph,
            'smsInfoGraph' => $smsInfoGraph,
            'chatInfoGraph' => $chatInfoGraph,
            'leadsInfoDataProvider' => $leadsInfoDataProvider,
            'leadsSearchModel' => $leadsSearchModel,
            'casesInfoDataProvider' => $casesInfoDataProvider,
            'casesSearchModel' => $casesSearchModel,
            'datePickerModel' => $datePickerModel
        ]);
    }
}
