<?php

namespace frontend\controllers;

use common\components\validators\PhoneValidator;
use common\models\CallUserAccess;
use common\models\Conference;
use common\models\Department;
use common\models\DepartmentPhoneProject;
use common\models\Employee;
use common\models\Lead;
use common\models\Notifications;
use common\models\PhoneBlacklist;
use common\models\Project;
use common\models\ProjectEmployeeAccess;
use common\models\query\CallQuery;
use common\models\search\LeadSearch;
use common\models\search\UserConnectionSearch;
use common\models\Sources;
use common\models\UserConnection;
use common\models\UserDepartment;
use common\models\UserGroupAssign;
use common\models\UserOnline;
use common\models\UserProfile;
use common\models\UserProjectParams;
use frontend\widgets\newWebPhone\call\socket\MissedCallMessage;
use http\Exception\InvalidArgumentException;
use src\auth\Auth;
use src\entities\cases\Cases;
use src\guards\call\CallDisplayGuard;
use src\guards\phone\PhoneBlackListGuard;
use src\helpers\app\AppHelper;
use src\helpers\call\CallHelper;
use src\helpers\setting\SettingHelper;
use src\model\call\abac\CallAbacObject;
use src\model\call\services\currentQueueCalls\CurrentQueueCallsService;
use src\model\call\services\reserve\CallReserver;
use src\model\call\services\reserve\Key;
use src\model\call\useCase\assignUsers\UsersForm;
use src\model\call\useCase\createCall\redialCall\CreateRedialCall;
use src\model\callLog\abac\CallLogAbacObject;
use src\model\callLog\abac\dto\CallLogRecordListenAbacDto;
use src\model\callLog\entity\callLog\CallLog;
use src\model\callLog\entity\callLog\CallLogQuery;
use src\model\callLog\entity\callLogRecord\CallLogRecord;
use src\model\callRecordingLog\entity\CallRecordingLog;
use src\model\conference\useCase\DisconnectFromAllActiveClientsCreatedConferences;
use src\model\callNote\useCase\addNote\CallNoteRepository;
use src\model\conference\useCase\PrepareCurrentCallsForNewCall;
use src\model\conference\useCase\ReturnToHoldCall;
use src\model\contactPhoneData\entity\ContactPhoneData;
use src\model\contactPhoneData\service\ContactPhoneDataDictionary;
use src\model\contactPhoneData\service\ContactPhoneDataService;
use src\model\contactPhoneList\entity\ContactPhoneList;
use src\model\contactPhoneList\service\ContactPhoneListService;
use src\model\leadRedial\assign\LeadRedialAccessChecker;
use src\model\leadRedial\assign\LeadRedialUnAssigner;
use src\model\leadRedial\job\CheckUserIsOnRedialCallJob;
use src\model\user\entity\userStatus\UserStatus;
use src\model\voip\phoneDevice\device\ReadyVoipDevice;
use src\repositories\call\CallRepository;
use src\repositories\call\CallUserAccessRepository;
use src\repositories\NotFoundException;
use src\services\call\CallService;
use src\services\cleaner\cleaners\CallCleaner;
use src\services\cleaner\form\DbCleanerParamsForm;
use src\services\phone\blackList\PhoneBlackListManageService;
use Yii;
use common\models\Call;
use common\models\search\CallSearch;
use yii\base\InvalidConfigException;
use yii\base\UnknownMethodException;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * CallController implements the CRUD actions for Call model.
 *
 * @property CallService $callService
 * @property CallRepository $callRepository
 * @property CallUserAccessRepository $callUserAccessRepository
 * @property CallNoteRepository $callNoteRepository
 * @property CurrentQueueCallsService $currentQueueCalls
 * @property PhoneBlackListManageService $phoneBlackListManageService
 */
class CallController extends FController
{
    private $callService;
    private $callRepository;
    private $callUserAccessRepository;
    private $callNoteRepository;
    private $currentQueueCalls;
    private $phoneBlackListManageService;

    public function __construct(
        $id,
        $module,
        CallService $callService,
        CallRepository $callRepository,
        CallUserAccessRepository $callUserAccessRepository,
        CallNoteRepository $callNoteRepository,
        CurrentQueueCallsService $currentQueueCalls,
        PhoneBlackListManageService $phoneBlackListManageService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->callService = $callService;
        $this->callRepository = $callRepository;
        $this->callUserAccessRepository = $callUserAccessRepository;
        $this->callNoteRepository = $callNoteRepository;
        $this->currentQueueCalls = $currentQueueCalls;
        $this->phoneBlackListManageService = $phoneBlackListManageService;
    }

    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    //'cancel' => ['POST'],
                ],
            ],
            'access' => [
                'allowActions' => [
                    'get-users-for-call', 'list-api', 'static-data-api', 'record', 'ajax-add-phone-black-list'
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function actionIndex(): string
    {
        $searchModel = new CallSearch();

        $params = Yii::$app->request->queryParams;

        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $dataProvider = $searchModel->search($params, $user);

        $userMonitorCleaner = new CallCleaner();
        $dbCleanerParamsForm = (new DbCleanerParamsForm())
            ->setTable($userMonitorCleaner->getTable())
            ->setColumn($userMonitorCleaner->getColumn());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'modelCleaner' => $dbCleanerParamsForm,
        ]);
    }

    /**
     * Lists all Call models.
     * @return mixed
     */
    public function actionList()
    {
        $searchModel = new CallSearch();

        $params = Yii::$app->request->queryParams;
        $params['CallSearch']['c_created_user_id'] = Yii::$app->user->id;

        $dataProvider = $searchModel->searchAgent($params);

        $phoneList = Employee::getPhoneList(Yii::$app->user->id);
        $projectList = \common\models\Project::getListByUser(Yii::$app->user->id);


        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'phoneList'          => $phoneList,
            'projectList'       => $projectList,
        ]);
    }


    /**
     * Displays a single Call model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);


        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Call model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView2($id)
    {
        $model = $this->findModel($id);
        $this->checkAccess($model);

        if ($model->c_is_new) {
            //$model->c_read_dt = date('Y-m-d H:i:s');
            $model->c_is_new = false;
            $model->save();
        }

        return $this->render('view2', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Call model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Call();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->c_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Call model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->c_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Call model.
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
     * Finds the Call model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Call the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Call::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findModelBySid($sid)
    {
        if (($model = Call::findOne(['c_call_sid' => $sid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /**
     * @param Call $model
     * @throws ForbiddenHttpException
     */
    protected function checkAccess(Call $model): void
    {
        /*$phoneList = [];

        $phoneList[$model->c_to] = $model->c_to;
        $phoneList[$model->c_from] = $model->c_from;

        $access = UserProjectParams::find()->where(['upp_user_id' => Yii::$app->user->id])
            ->andWhere(['upp_tw_phone_number' => $phoneList])->exists();*/


        $access = $model->c_created_user_id === Yii::$app->user->id ? true : false;


        if (!$access) {
            throw new ForbiddenHttpException('Access denied for this Call. '); // Check User Project Params phones
        }
    }


    /**
     * @return \yii\web\Response
     */
    public function actionAllRead()
    {
        Call::updateAll(['c_is_new' => false], ['c_is_new' => true, 'c_created_user_id' => Yii::$app->user->id]);
        return $this->redirect(['list']);
    }


    public function actionUserMap()
    {
        $this->layout = '@frontend/themes/gentelella_v2/views/layouts/main_tv';

        $user = Auth::user();
        $isAdmin = $user->isSuperAdmin() || $user->isOnlyAdmin();

        $callSearch = new CallSearch();
        $userConnectionSearch = new UserConnectionSearch();
        $params = Yii::$app->request->queryParams;

        $withOutDepartments = 0;
        if ($isAdmin) {
            $accessDepartments = [];
        } elseif ($departments = $user->udDeps) {
            $accessDepartments = ArrayHelper::getColumn($departments, 'dep_id');
        } else {
            $accessDepartments = [$withOutDepartments];
        }

        $withOutProjects = 0;
        if ($isAdmin) {
            $accessProjects = [];
        } elseif ($projects = $user->projects) {
            $accessProjects = ArrayHelper::getColumn($projects, 'id');
        } else {
            $accessProjects = [$withOutProjects];
        }

//        $withOutGroups = 0;
//        if ($isAdmin) {
//            $accessGroups = [];
//        } elseif ($groups = $user->ugsGroups) {
//            $accessGroups = ArrayHelper::getColumn($groups, 'ug_id');
//        } else {
//            $accessGroups = [$withOutGroups];
//        }
        $accessGroups = [];

        $params['UserConnectionSearch']['ug_ids'] = $accessGroups;
        $params['UserConnectionSearch']['project_ids'] = $accessProjects;

        if ($isAdmin || in_array(Department::DEPARTMENT_SALES, $accessDepartments, true)) {
            $params['UserConnectionSearch']['dep_id'] = Department::DEPARTMENT_SALES;
            $salesOnline = $userConnectionSearch->searchUsersByCallMap($params);
        } else {
            $salesOnline = null;
        }

        if ($isAdmin || in_array(Department::DEPARTMENT_EXCHANGE, $accessDepartments, true)) {
            $params['UserConnectionSearch']['dep_id'] = Department::DEPARTMENT_EXCHANGE;
            $exchangeOnline = $userConnectionSearch->searchUsersByCallMap($params);
        } else {
            $exchangeOnline = null;
        }

        if ($isAdmin || in_array(Department::DEPARTMENT_SUPPORT, $accessDepartments, true)) {
            $params['UserConnectionSearch']['dep_id'] = Department::DEPARTMENT_SUPPORT;
            $supportOnline = $userConnectionSearch->searchUsersByCallMap($params);
        } else {
            $supportOnline = null;
        }

        if ($isAdmin || in_array(Department::DEPARTMENT_SCHEDULE_CHANGE, $accessDepartments, true)) {
            $params['UserConnectionSearch']['dep_id'] = Department::DEPARTMENT_SCHEDULE_CHANGE;
            $scheduleChangeOnline = $userConnectionSearch->searchUsersByCallMap($params);
        } else {
            $scheduleChangeOnline = null;
        }

        if ($isAdmin || in_array($withOutDepartments, $accessDepartments, true)) {
            $params['UserConnectionSearch']['dep_id'] = $withOutDepartments;
            $withoutDepartmentOnline = $userConnectionSearch->searchUsersByCallMap($params);
        } else {
            $withoutDepartmentOnline = null;
        }

        $params['CallSearch']['dep_ids'] = $accessDepartments;
        $params['CallSearch']['project_ids'] = $accessProjects;
        $params['CallSearch']['ug_ids'] = $accessGroups;

        $params['CallSearch']['status_ids'] = [Call::STATUS_IN_PROGRESS, Call::STATUS_RINGING, Call::STATUS_QUEUE, Call::STATUS_IVR, Call::STATUS_DELAY];
        $activeCalls = $callSearch->searchUserCallMap($params);

        $params['CallSearch']['status_ids'] = [Call::STATUS_COMPLETED, Call::STATUS_BUSY, Call::STATUS_FAILED, Call::STATUS_NO_ANSWER, Call::STATUS_CANCELED];
        $params['CallSearch']['limit'] = 10;
        $historyCalls = $callSearch->searchUserCallMapHistory($params);

        return $this->render('user-map/user-map', [
            'salesOnline' => $salesOnline,
            'exchangeOnline' => $exchangeOnline,
            'supportOnline' => $supportOnline,
            'scheduleChangeOnline' => $scheduleChangeOnline,
            'withoutDepartmentOnline' => $withoutDepartmentOnline,
            'historyCalls' => $historyCalls,
            'activeCalls' => $activeCalls,
        ]);
    }


    public function actionRealtimeUserMap()
    {
        if (Yii::$app->request->isPost) {
            $user = Auth::user();
            $searchUserConnectionModel = new UserConnectionSearch();
            $searchUserCallModel = new CallSearch();
            //$params = Yii::$app->request->queryParams;

            $accessDepartmentModels = $user->udDeps;
            if ($accessDepartmentModels) {
                $accessDepartments = ArrayHelper::map($accessDepartmentModels, 'dep_id', 'dep_id');
            } else {
                $accessDepartments = [];
            }

            $isSuper = ($user->isSupervision() || $user->isExSuper() || $user->isSupSuper());
            if ($isSuper && !in_array(Department::DEPARTMENT_SUPPORT, $accessDepartments, true)) {
                $userGroupsModel = $user->ugsGroups;

                if ($userGroupsModel) {
                    $userGroups = ArrayHelper::map($userGroupsModel, 'ug_id', 'ug_id');
                } else {
                    $userGroups = [];
                }

                $params['UserConnectionSearch']['ug_ids'] = $userGroups;
                $params['CallSearch']['ug_ids'] = $userGroups;
            }

            if (!$accessDepartments || in_array(Department::DEPARTMENT_SALES, $accessDepartments, true)) {
                $params['UserConnectionSearch']['dep_id'] = Department::DEPARTMENT_SALES;
                $usersOnlineDepSales = $searchUserConnectionModel->searchRealtimeUserCallMap($params);
            } else {
                $usersOnlineDepSales = [];
            }

            if (!$accessDepartments || in_array(Department::DEPARTMENT_EXCHANGE, $accessDepartments, true)) {
                $params['UserConnectionSearch']['dep_id'] = Department::DEPARTMENT_EXCHANGE;
                $usersOnlineDepExchange = $searchUserConnectionModel->searchRealtimeUserCallMap($params);
            } else {
                $usersOnlineDepExchange = [];
            }

            if (!$accessDepartments || in_array(Department::DEPARTMENT_SUPPORT, $accessDepartments, true)) {
                $params['UserConnectionSearch']['dep_id'] = Department::DEPARTMENT_SUPPORT;
                $usersOnlineDepSupport = $searchUserConnectionModel->searchRealtimeUserCallMap($params);
            } else {
                $usersOnlineDepSupport = [];
            }

            if (!$accessDepartments) {
                $params['UserConnectionSearch']['dep_id'] = 0;
                $usersOnline = $searchUserConnectionModel->searchRealtimeUserCallMap($params);
            } else {
                $usersOnline = [];
            }

            $params['CallSearch']['dep_ids'] = $accessDepartments;
            $params['CallSearch']['status_ids'] = [Call::STATUS_IN_PROGRESS, Call::STATUS_RINGING, Call::STATUS_QUEUE, Call::STATUS_IVR, Call::STATUS_DELAY];
            $realtimeCalls = $searchUserCallModel->searchRealtimeUserCallMap($params);

            $params['CallSearch']['status_ids'] = [Call::STATUS_COMPLETED, Call::STATUS_BUSY, Call::STATUS_FAILED, Call::STATUS_NO_ANSWER, Call::STATUS_CANCELED];

            $callsHistory = $searchUserCallModel->searchRealtimeUserCallMapHistory($params);

            /*CentrifugoService::sendMsg(json_encode([
                'onlineDepSales' => $usersOnlineDepSales,
                'onlineDepExchange' => $usersOnlineDepExchange,
                'onlineDepSupport' => $usersOnlineDepSupport,
                'usersOnline' => $usersOnline,
                'realtimeCalls' => $realtimeCalls,
                'callsHistory' => $callsHistory
            ]), 'realtimeUserMapChannel#' . Auth::id());*/

            Yii::$app->centrifugo->setSafety(false)->publish('realtimeUserMapChannel#' . Auth::id(), ['message' => json_encode([
                'onlineDepSales' => $usersOnlineDepSales,
                'onlineDepExchange' => $usersOnlineDepExchange,
                'onlineDepSupport' => $usersOnlineDepSupport,
                'usersOnline' => $usersOnline,
                'realtimeCalls' => $realtimeCalls,
                'callsHistory' => $callsHistory
            ])]);

            return $this->asJson(['updatedTime' => Yii::$app->formatter->asTime(time(), 'php:H:i:s')]);
        } else {
            $this->layout = '@frontend/themes/gentelella_v2/views/layouts/main_tv';

            return $this->render('realtime-user-map/index');
        }
    }

    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function actionRealtimeMap(): string
    {
//        $this->isAutoLogoutEnabled = false;
//        $this->isIdleMonitorEnabled = false;

        $centrifugoEnabled = Yii::$app->params['centrifugo']['enabled'] ?? false;
        $centrifugoWsConnectionUrl = Yii::$app->params['centrifugo']['wsConnectionUrl'] ?? '';

        if (!$centrifugoEnabled) {
            throw new InvalidConfigException('The "centrifugo" is not enabled.');
        }

        if (empty($centrifugoWsConnectionUrl)) {
            throw new InvalidConfigException('The "wsConnectionUrl" property must be set in config params.');
        }

        $this->layout = '@frontend/themes/gentelella_v2/views/layouts/main_tv2';
        $cfChannelName = Call::CHANNEL_REALTIME_MAP;
        $userOnlineChannel = Call::CHANNEL_USER_ONLINE;
        $userStatusChannel = UserStatus::CHANNEL_NAME;

        return $this->render('realtime-map', [
            //'cfChannels' => [$cfChannelName],
            'cfChannelName' => $cfChannelName,
            'cfUserOnlineChannel' => $userOnlineChannel,
            'cfUserStatusChannel' => $userStatusChannel,
            'cfConnectionUrl' => $centrifugoWsConnectionUrl,
            'cfToken' => Yii::$app->centrifugo->generateConnectionToken(Auth::id())
        ]);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function actionListApi(): array
    {
        $response = [];
        Yii::$app->response->format = Response::FORMAT_JSON;

        $callList = [];
        $calls = Call::find()->where(['c_status_id' =>
            [
                Call::STATUS_IVR, Call::STATUS_QUEUE, Call::STATUS_IN_PROGRESS,
                Call::STATUS_RINGING, Call::STATUS_HOLD, Call::STATUS_DELAY
            ]
        ])
            //->andWhere(['c_id' => 1097179])
            ->orderBy(['c_id' => SORT_DESC])
            ->limit(1000)->all();

        if ($calls) {
            foreach ($calls as $call) {
                $callList[] = $call->getApiData();
            }
        }
        $response['callList'] = $callList;
        return $response;
    }

    /**
     * @return array
     */
    public function actionStaticDataApi(): array
    {
        $response = [];
        Yii::$app->response->format = Response::FORMAT_JSON;

        $response['projectList'] = Project::getList();
        $response['depList'] = Department::DEPARTMENT_LIST;
        $response['userList'] = Employee::getList();

        $response['callStatusList'] = Call::STATUS_LIST;
        $response['callSourceList'] = Call::SHORT_SOURCE_LIST;
        $response['callTypeList'] = Call::TYPE_LIST;
        $response['callUserAccessStatusTypeList'] = CallUserAccess::STATUS_TYPE_LIST;
        $response['onlineUserList'] = UserOnline::find()->all();
        $response['userStatusList'] = UserStatus::find()->all();


        /** @var Employee $user */
        $user = \Yii::$app->user->identity;
        $response['userTimeZone'] = $user->timezone ?: 'UTC';

        return $response;
    }




    public function actionUserMap2()
    {
        $this->layout = '@frontend/themes/gentelella_v2/views/layouts/main_tv';

        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        $searchModel = new CallSearch();
        $searchModel2 = new UserConnectionSearch();
        $params = Yii::$app->request->queryParams;

        //if (Yii::$app->user->identity->canRole('supervision')) {
        //$params['CallSearch']['supervision_id'] = $userId;
        //$params['CallSearch']['status'] = Employee::STATUS_ACTIVE;
        //}

        $accessDepartmentModels = $user->udDeps;

        if ($accessDepartmentModels) {
            $accessDepartments = ArrayHelper::map($accessDepartmentModels, 'dep_id', 'dep_id');
        } else {
            $accessDepartments = [];
        }

        $isSuper = ($user->isSupervision() || $user->isExSuper() || $user->isSupSuper());

        if ($isSuper && !in_array(Department::DEPARTMENT_SUPPORT, $accessDepartments, true)) {
            $userGroupsModel = $user->ugsGroups;

            if ($userGroupsModel) {
                $userGroups = ArrayHelper::map($userGroupsModel, 'ug_id', 'ug_id');
            } else {
                $userGroups = [];
            }

            $params['UserConnectionSearch']['ug_ids'] = $userGroups;
            $params['CallSearch']['ug_ids'] = $userGroups;
        }

        //VarDumper::dump($accessDepartments, 10, true); exit;


        if (!$accessDepartments || in_array(Department::DEPARTMENT_SALES, $accessDepartments, true)) {
            $params['UserConnectionSearch']['dep_id'] = Department::DEPARTMENT_SALES;
            $dataProviderOnlineDep1 = $searchModel2->searchUserCallMap($params);
        } else {
            $dataProviderOnlineDep1 = null;
        }

        if (!$accessDepartments || in_array(Department::DEPARTMENT_EXCHANGE, $accessDepartments, true)) {
            $params['UserConnectionSearch']['dep_id'] = Department::DEPARTMENT_EXCHANGE;
            $dataProviderOnlineDep2 = $searchModel2->searchUserCallMap($params);
        } else {
            $dataProviderOnlineDep2 = null;
        }

        if (!$accessDepartments || in_array(Department::DEPARTMENT_SUPPORT, $accessDepartments, true)) {
            $params['UserConnectionSearch']['dep_id'] = Department::DEPARTMENT_SUPPORT;
            $dataProviderOnlineDep3 = $searchModel2->searchUserCallMap($params);
        } else {
            $dataProviderOnlineDep3 = null;
        }

        if (!$accessDepartments) {
            $params['UserConnectionSearch']['dep_id'] = 0;
            $dataProviderOnline = $searchModel2->searchUserCallMap($params);
        } else {
            $dataProviderOnline = null;
        }

        $params['CallSearch']['dep_ids'] = $accessDepartments;
        $params['CallSearch']['status_ids'] = [Call::STATUS_IN_PROGRESS, Call::STATUS_RINGING, Call::STATUS_QUEUE, Call::STATUS_IVR, Call::STATUS_DELAY];
        $dataProvider3 = $searchModel->searchUserCallMap($params);

        $params['CallSearch']['status_ids'] = [Call::STATUS_COMPLETED, Call::STATUS_BUSY, Call::STATUS_FAILED, Call::STATUS_NO_ANSWER, Call::STATUS_CANCELED];
        $params['CallSearch']['limit'] = 10;
        $dataProvider2 = $searchModel->searchUserCallMapHistory($params);

        //$searchModel->datetime_start = date('Y-m-d', strtotime('-0 day'));
        //$searchModel->datetime_end = date('Y-m-d');

        //$searchModel->date_range = $searchModel->datetime_start.' - '. $searchModel->datetime_end;


        return $this->render('user-map2/user-map2', [
            'dataProviderOnlineDep1' => $dataProviderOnlineDep1,
            'dataProviderOnlineDep2' => $dataProviderOnlineDep2,
            'dataProviderOnlineDep3' => $dataProviderOnlineDep3,
            'dataProviderOnline' => $dataProviderOnline,


            'dataProvider2' => $dataProvider2,
            'dataProvider3' => $dataProvider3,
            //'searchModel' => $searchModel,
        ]);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAjaxMissedCalls()
    {
        $searchModel = new CallSearch();

        $params = Yii::$app->request->queryParams;
        $params['CallSearch']['c_created_user_id'] = Yii::$app->user->id;
        $params['CallSearch']['c_call_type_id'] = Call::CALL_TYPE_IN;
        // $params['CallSearch']['c_call_status'] = Call::TW_STATUS_NO_ANSWER;
        $params['CallSearch']['c_status_id'] = Call::STATUS_NO_ANSWER;
        $params['CallSearch']['c_call_type_id'] = Call::CALL_TYPE_IN;

        $params['CallSearch']['limit'] = 20;
        //$params['CallSearch']['sort'] = false;

        $dataProvider = $searchModel->searchAgent($params);

        foreach ($dataProvider->models as $model) {
            if ($model->c_is_new) {
                $model->c_is_new = false;
                $model->update(false);
            }
        }
        //$dataProvider->sort->so

        return $this->renderPartial('ajax_missed_calls', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionClearMissedCalls(): Response
    {
        $count = 0;
        $missedCalls = Call::find()->byCreatedUser(Auth::id())->missed()->all();
        foreach ($missedCalls as $missedCall) {
            $missedCall->c_is_new = false;
            if (!$missedCall->save()) {
                Yii::error($missedCall->getErrors(), 'actionClearMissedCalls');
                $count++;
            }
        }

        $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? MissedCallMessage::updateCount($count) : [];
        Notifications::publish(MissedCallMessage::COMMAND, ['user_id' => Auth::id()], $dataNotification);

        return $this->asJson([
            'count' => $count
        ]);
    }

    /**
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionAjaxCallInfo()
    {
        $id = (int) Yii::$app->request->post('id');
        $sid = (string) Yii::$app->request->post('sid');

        if ($id) {
            $model = $this->findModel($id);
        } else {
            $model = $this->findModelBySid($sid);
        }

        $this->checkAccess($model);

        if ($model->c_is_new) {
            //$model->c_read_dt = date('Y-m-d H:i:s');
            $model->c_is_new = false;
            $model->update();
        }

        return $this->renderAjax('ajax_call_info', [
            'model' => $model,
        ]);
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionCancelManual(): Response
    {
        if (!$callId = (int)Yii::$app->request->post('id')) {
            throw new BadRequestHttpException();
        }

        try {
            $this->callService->cancelByCrash($callId, Yii::$app->user->id);
            return $this->asJson(['success' => true]);
        } catch (\DomainException $e) {
            return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionAjaxCallCancel()
    {
        $id = (int) Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $this->checkAccess($model);

        if ($model->isStatusRinging() || $model->isStatusInProgress()) {
            $model->setStatusFailed();
            if ($model->update() !== false) {
                $model->cancelCall();
            }
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionCancel()
    {
        $id = (int) Yii::$app->request->get('id');
        $model = $this->findModel($id);

        if ($result = $model->cancelCall()) {
            Yii::$app->session->setFlash('success', '<strong>Cancel Call</strong> Success');
        } else {
            Yii::$app->session->setFlash('error', '<strong>Cancel Call</strong> Error');
        }

        return $this->redirect(['index']);
    }

    public function actionAjaxAcceptIncomingCall(): Response
    {
        $action = \Yii::$app->request->post('act');
        $call_sid = \Yii::$app->request->post('call_sid');
        $deviceId = (int)\Yii::$app->request->post('deviceId');

        try {
            $voipDevice = (new ReadyVoipDevice())->find($deviceId, Auth::id());
        } catch (\Throwable $e) {
            return $this->asJson([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }

        $response = [
            'error' => true,
            'message' => 'Internal Server Error'
        ];
        if ($action && $call_sid) {
            try {
                $call = $this->callRepository->findBySid($call_sid);

                $callUserAccess = CallUserAccess::find()->where([
                    'cua_user_id' => Auth::id(),
                    'cua_call_id' => $call->c_id,
                    'cua_status_id' => CallUserAccess::STATUS_TYPE_PENDING
                ])->one();

                $reserver = Yii::createObject(CallReserver::class);

                if ($callUserAccess) {
                    $userId = Auth::id();
                    switch ($action) {
                        case 'accept':
                            $key = Key::byAcceptCall($callUserAccess->cua_call_id);
                            $isReserved = $reserver->reserve($key, $userId);
                            if ($isReserved) {
                                $prepare = new PrepareCurrentCallsForNewCall($userId);
                                if ($prepare->prepare()) {
                                    $this->callService->acceptCall($callUserAccess, $userId, $voipDevice);
                                    Yii::createObject(LeadRedialUnAssigner::class)->acceptCall($userId);
                                }
                            } else {
                                Notifications::publish('callAlreadyTaken', ['user_id' => $userId], ['callSid' => $call->c_call_sid]);
                                Yii::info(VarDumper::dumpAsString([
                                    'callId' => $callUserAccess->cua_call_id,
                                    'userId' => $userId,
                                    'acceptedUserId' => $reserver->getReservedUser($key),
                                ]), 'info\NewPhoneWidgetAcceptRedisReservation');
                            }

                            $response['error'] = false;
                            $response['message'] = 'success';
                            break;
                        case 'busy':
                            $this->callService->busyCall($callUserAccess, Auth::user());
                            $response['error'] = false;
                            $response['message'] = 'success';
                            break;
                    }
                } else {
                    Notifications::publish('callAlreadyTaken', ['user_id' => Auth::id()], ['callSid' => $call->c_call_sid]);
                    $response = [
                        'error' => false,
                        'message' => '',
                    ];
                }
            } catch (\RuntimeException | NotFoundException $e) {
                $response['message'] = $e->getMessage();
            }
        }

        return $this->asJson($response);
    }

    public function actionAjaxAcceptWarmTransferCall(): Response
    {
        $call_sid = (string)\Yii::$app->request->post('call_sid');

        $deviceId = (int)\Yii::$app->request->post('deviceId');

        try {
            $voipDevice = (new ReadyVoipDevice())->find($deviceId, Auth::id());
        } catch (\Throwable $e) {
            return $this->asJson([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }

        $response = [
            'error' => true,
            'message' => 'Internal Server Error'
        ];
        try {
            $call = $this->callRepository->findBySid($call_sid);

            $callUserAccess = CallUserAccess::find()->where([
                'cua_user_id' => Auth::id(),
                'cua_call_id' => $call->c_id,
                'cua_status_id' => CallUserAccess::STATUS_TYPE_WARM_TRANSFER
            ])->one();

            $reserver = Yii::createObject(CallReserver::class);

            if ($callUserAccess) {
                $userId = Auth::id();
                $key = Key::byWarmTransfer($callUserAccess->cua_call_id);
                $isReserved = $reserver->reserve($key, $userId);
                if ($isReserved) {
                    $prepare = new PrepareCurrentCallsForNewCall($userId);
                    if ($prepare->prepare()) {
                        $this->callService->acceptWarmTransferCall($callUserAccess, $userId, $voipDevice);
                        Yii::createObject(LeadRedialUnAssigner::class)->acceptCall($userId);
                    }
                } else {
                    Notifications::publish('callAlreadyTaken', ['user_id' => $userId], ['callSid' => $call->c_call_sid]);
                    Yii::info(VarDumper::dumpAsString([
                        'callId' => $callUserAccess->cua_call_id,
                        'userId' => $userId,
                        'acceptedUserId' => $reserver->getReservedUser($key),
                    ]), 'info\WarmTransferAccept');
                }

                $response['error'] = false;
                $response['message'] = 'success';
            } else {
                Notifications::publish('callAlreadyTaken', ['user_id' => Auth::id()], ['callSid' => $call->c_call_sid]);
                $response = [
                    'error' => false,
                    'message' => '',
                ];
            }
        } catch (\RuntimeException | \DomainException | NotFoundException $e) {
            $response['message'] = $e->getMessage();
        }

        return $this->asJson($response);
    }

    public function actionAjaxAcceptPriorityCall(): Response
    {
        if (!SettingHelper::isGeneralLinePriorityEnable()) {
            throw new NotFoundHttpException();
        }

        $deviceId = (int)\Yii::$app->request->post('deviceId');

        try {
            $voipDevice = (new ReadyVoipDevice())->find($deviceId, Auth::id());
        } catch (\Throwable $e) {
            return $this->asJson([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }

        $response = [
            'error' => false,
            'message' => '',
            'isRedialCall' => false,
        ];
        $userId = Auth::id();

        try {
            $callUserAccess = CallUserAccess::find()
                ->select([CallUserAccess::tableName() . '.*'])
                ->addSelect(['is_owner' => new Expression('if ((' . Lead::tableName() . '.employee_id is not null and ' . Lead::tableName() . '.employee_id = cua_user_id) or (cs_user_id is not null and cs_user_id = cua_user_id), 1, 0)')])
                ->innerJoin(Call::tableName(), 'c_id = cua_call_id')
                ->leftJoin(Lead::tableName(), Lead::tableName() . '.id = c_lead_id')
                ->leftJoin(Cases::tableName(), 'cs_id = c_case_id')
                ->andWhere([
                    'cua_user_id' => Auth::id(),
                    'cua_status_id' => CallUserAccess::STATUS_TYPE_PENDING
                ])
                ->andWhere([
                    'OR',
                    ['c_source_type_id' => [Call::SOURCE_GENERAL_LINE, Call::SOURCE_REDIRECT_CALL]],
                    [
                        'AND',
                        ['c_source_type_id' => [Call::SOURCE_DIRECT_CALL]],
                        ['<>', 'c_created_user_id', new Expression('cua_user_id')],
                    ],
                ])
                ->andWhere(['<>', 'c_status_id', Call::STATUS_HOLD])
                ->orderBy([
                    'cua_priority' => SORT_DESC,
                    'is_owner' => SORT_DESC,
                    'cua_created_dt' => SORT_ASC
                ])->all();

            $reserver = Yii::createObject(CallReserver::class);

            $isReserved = false;
            foreach ($callUserAccess as $access) {
                $isReserved = $reserver->reserve(Key::byAcceptCall($access->cua_call_id), $userId);
                if (!$isReserved) {
                    continue;
                }
                $prepare = new PrepareCurrentCallsForNewCall($userId);
                if ($prepare->prepare()) {
                    $this->callService->acceptCall($access, $userId, $voipDevice);
                    Yii::createObject(LeadRedialUnAssigner::class)->acceptCall($userId);
                }
                break;
            }

            if (!$isReserved) {
                $autoRedialIsEnabled = (bool)UserProfile::find()->select(['up_auto_redial'])->andWhere(['up_user_id' => Auth::id()])->scalar();
                if (SettingHelper::leadRedialEnabled() && $autoRedialIsEnabled && Yii::createObject(LeadRedialAccessChecker::class)->exist($userId)) {
                    $leadRedialQueue = Yii::createObject(\src\model\leadRedial\queue\LeadRedialQueue::class);
                    $redialCall = $leadRedialQueue->getCall(Auth::user());
                    if ($redialCall) {
                        $prepare = new PrepareCurrentCallsForNewCall($userId);
                        if ($prepare->prepare()) {
                            UserStatus::isOnCallOn($userId);
                            Yii::createObject(LeadRedialUnAssigner::class)->acceptRedialCall($userId, $redialCall->leadId);
                            $job = new CheckUserIsOnRedialCallJob($userId, $redialCall->leadId, date('Y-m-d H:i:s'));
                            $delay = SettingHelper::getRedialCheckIsOnCallTime();
                            $job->delayJob = $delay;
                            \Yii::$app->queue_job->delay($delay)->push($job);
                            $response['isRedialCall'] = true;
                            try {
                                $result = (new CreateRedialCall())($redialCall, $voipDevice);
                                if ($result['error']) {
                                    $response['redialError'] = $result['message'];
                                }
                            } catch (\Throwable $t) {
                                $response['redialError'] = $t->getMessage();
                                UserStatus::isOnCallOff($userId);
                                Yii::error(AppHelper::mergeThrowableWithData($t, ['userId' => $redialCall->userId, 'leadId' => $redialCall->leadId]), 'RedialCallAccepted');
                            }
                        } else {
                            $response['error'] = true;
                            $response['message'] = 'Processing current call error. Please try again.';
                        }
                    } else {
                        Yii::createObject(LeadRedialUnAssigner::class)->emptyQueue($userId);
                        $response['error'] = true;
                        $response['message'] = 'Phone line queue is empty.';
                    }
                } else {
                    Yii::createObject(LeadRedialUnAssigner::class)->emptyQueue($userId);
                    $response['error'] = true;
                    $response['message'] = 'Phone line queue is empty.';
                }
            }
        } catch (\RuntimeException | NotFoundException $e) {
            $response['error'] = true;
            $response['message'] = $e->getMessage();
        } catch (\Throwable $e) {
            Yii::error([
                'message' => 'Accept priority call',
                'error' => $e->getMessage(),
                'userId' => $userId,
            ], 'CallController::actionAjaxAcceptPriorityCall');
            $response['error'] = true;
            $response['message'] = 'Internal server error.';
        }

        return $this->asJson($response);
    }

    public function actionReturnHoldCall(): Response
    {
        $call_sid = \Yii::$app->request->post('call_sid');

        $deviceId = (int)\Yii::$app->request->post('deviceId');

        try {
            $voipDevice = (new ReadyVoipDevice())->find($deviceId, Auth::id());
        } catch (\Throwable $e) {
            return $this->asJson([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }

        $response = [
            'error' => true,
            'message' => 'Internal Server Error'
        ];

        if ($call_sid) {
            try {
                $userId = Auth::id();
                $call = $this->callRepository->findBySid($call_sid);
                $callUserAccess = CallUserAccess::find()->where(['cua_user_id' => $userId, 'cua_call_id' => $call->c_id, 'cua_status_id' => CallUserAccess::STATUS_TYPE_PENDING])->one();
                if (!$callUserAccess) {
                    throw new \DomainException('Not found call user access');
                }
                $prepare = new PrepareCurrentCallsForNewCall($userId);
                if (!$prepare->prepare()) {
                    throw new \DomainException('Prepare current calls error');
                }

                $return = new ReturnToHoldCall();
                if (!$return->return($call, $userId, $voipDevice)) {
                    $this->validateCallStatus($call);
                    throw new \DomainException('Return Hold call error');
                }

                if (!$return->acceptHoldCall($callUserAccess)) {
                    throw new \DomainException('Accept Hold call error');
                }

                Yii::createObject(LeadRedialUnAssigner::class)->acceptCall($userId);

                $response = [
                    'error' => false,
                    'message' => ''
                ];
            } catch (\DomainException $e) {
                $response['message'] = $e->getMessage();
            }
        }

        return $this->asJson($response);
    }

    private function validateCallStatus(Call $call): void
    {
        $result = $this->getCallInfo($call->c_call_sid);
        if (isset($result['status']) && $result['status'] === Call::TW_STATUS_COMPLETED) {
            $call->c_call_status = $result['status'];
            $call->setStatusByTwilioStatus($call->c_call_status);
            if (!$call->save()) {
                Yii::error([
                    'errors' => $call->getErrors(),
                    'model' => $call->getAttributes(),
                ], 'CallController:checkCallStatusIsActive:Call:save');
            }
        }
    }

    private function getCallInfo(string $callSid): array
    {
        try {
            $result = \Yii::$app->communication->getCallInfo($callSid);
            if ($result['error']) {
                \Yii::error(VarDumper::dumpAsString([
                    'result' => $result,
                    'callSid' => $callSid,
                ]), 'CallController:getCallInfo:Result');
            } else {
                if (!empty($result['result'])) {
                    return $result['result'];
                }
                Yii::error([
                    'message' => 'Not found result',
                    'callSid' => $callSid,
                ], 'CallController:getCallInfo:Result');
            }
        } catch (\Throwable $e) {
            \Yii::error(VarDumper::dumpAsString([
                'error' => AppHelper::throwableFormatter($e),
                'callSid' => $callSid,
            ]), 'CallController:getCallInfo:Throwable');
        }
        return [];
    }

    /**
     * @param string $callSid
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionRecord(string $callSid): void
    {
        $cacheKey = 'call-recording-url-' . $callSid . '-user-' . Auth::id();

        try {
            $callRecordSid = Yii::$app->cacheFile->get($cacheKey);
            if (!$callRecordSid) {
                $callLogRecord = CallLogQuery::getCallLogRecordByCallSid($callSid);

                if ($callLogRecord && !empty($callLogRecord['clr_record_sid'])) {
                    $callRecordSid = $callLogRecord['clr_record_sid'];
                    $callRecordDuration = $callLogRecord['clr_duration'];
                } elseif ($call = Call::find()->selectRecordingData()->bySid($callSid)->asArray()->one()) {
                    $callRecordSid = $call['c_recording_sid'];
                    $callRecordDuration = $call['c_recording_duration'];
                } else {
                    throw new NotFoundException('Call not found');
                }

                Yii::$app->cacheFile->set($cacheKey, $callRecordSid, $callRecordDuration  + SettingHelper::getCallRecordingLogAdditionalCacheTimeout());

                if (SettingHelper::isCallRecordingLogEnabled()) {
                    $callRecordingLog = CallRecordingLog::create($callSid, Auth::id(), (int)date('Y'), (int)date('m'));
                    if (!$callRecordingLog->save(true)) {
                        Yii::error('Call Recording Log saving failed: ' . $callRecordingLog->getErrorSummary(false)[0], 'CallController::actionCallRecordingLog::callRecordingLog::save');
                    }
                }
            }

            if ($callRecordSid) {
                $dto = new CallLogRecordListenAbacDto(CallLog::findOne(['cl_call_sid' => $callSid]), Auth::user());
                if (\Yii::$app->abac->can($dto, CallAbacObject::OBJ_CALL_LOG, CallAbacObject::ACTION_LISTEN_RECORD, Auth::user())) {
                    header('X-Accel-Redirect: ' . Yii::$app->communication->xAccelRedirectCommunicationUrl . $callRecordSid);
                } else {
                    throw new ForbiddenHttpException('You can not hear this record');
                }
            }
        } catch (NotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        } catch (ForbiddenHttpException $e) {
            throw new ForbiddenHttpException($e->getMessage());
        }
    }

    public function actionAjaxAddNote(): Response
    {
        $callSid = Yii::$app->request->post('callSid');
        $note = Yii::$app->request->post('note');
        $callId = Yii::$app->request->post('callId');

        $result = [
            'error' => false,
            'message' => 'Note successfully added'
        ];

        try {
            $call = $this->callRepository->findByCallSidOrCallId((string)$callSid, (int)$callId);
            if (!$call->isOwner(Auth::id())) {
                throw new \RuntimeException('Is not your call');
            }
            $this->callNoteRepository->add($call->c_id, $note);
        } catch (\RuntimeException $e) {
            $result['error'] = true;
            $result['message'] = $e->getMessage();
        } catch (\Throwable $e) {
            $result['error'] = true;
            $result['message'] = 'Internal Server Error;';
        }

        return $this->asJson($result);
    }

    /**
     * @return array|\yii\db\ActiveRecord|null
     */
    public function actionReactInitCallWidget()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $calls = Call::find()->where(['c_created_user_id' => Yii::$app->user->id])->orderBy(['c_id' => SORT_DESC])->limit(3)->all();
        return ['calls' => $calls];
    }

    public function actionAjaxCallLogInfo()
    {
        $sid = (string) Yii::$app->request->post('sid');

        $model = $this->findCallLogModel($sid);

        if (!$model->isOwner(Auth::id())) {
            throw new ForbiddenHttpException('Access denied.');
        }

        return $this->renderAjax('ajax_call_log_info', [
            'model' => $model,
        ]);
    }

    public function actionGetUsersForCall($id)
    {
        $call = $this->findModel($id);
        if (!Auth::can('call/assignUsers', ['call' => $call])) {
            if (Yii::$app->request->getIsGet()) {
                return '<h5>Call ID: ' . $call->c_id . ' (' . $call->getCallTypeName() . ') </h5> Access denied.';
            }
            return "<script> $('#modal-df').modal('hide');createNotify('Add users to call', 'Access denied.', 'error');</script>";
        }

        $groups = [];
        $users = $this->getAvailableUsers($call, $groups);
        if (Yii::$app->request->getIsGet()) {
            if (!$users) {
                return '<h5>Call ID: ' . $call->c_id . ' (' . $call->getCallTypeName() . ') </h5>Users not found';
            }
        }

        $model = new UsersForm($users);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $result = $this->addUsersForCall($call, $model->selectedUsers);
            if ($result) {
                return "<script> $('#modal-df').modal('hide');createNotify('Add users to call', 'Done', 'success');pjaxReload({container: '#pjax-call-list', 'timeout': 4000});</script>";
            }
            return "<script> $('#modal-df').modal('hide');createNotify('Add users to call', 'Server error. Please try again later.', 'danger');</script>";
        }

        return $this->renderAjax('get_users_for_call', [
            'model' => $model,
            'call' => $call
        ]);
    }

    public function actionGetCallInfo(): string
    {
        $callSid = Yii::$app->request->get('sid', '');

        $call = $this->findCallModel($callSid);

        $callGuard = new CallDisplayGuard();
        return $this->renderAjax('monitor/_call_info', [
            'call' => $call,
            'callGuard' => $callGuard
        ]);
    }

    public function actionAjaxAddPhoneBlackList(): Response
    {
        if (!Yii::$app->request->isPost) {
            throw new MethodNotAllowedHttpException('Request is not post');
        }

        if (!PhoneBlackListGuard::canAdd(Auth::id())) {
            throw new ForbiddenHttpException('You do not have access to perform this action');
        }
        $enableNotifier = true;
        $phone = Yii::$app->request->post('phone', '');
        try {
            $phoneBlackList = PhoneBlacklist::findOne(['pbl_phone' => $phone]);
            if ($phoneBlackList) {
                if (!$phoneBlackList->pbl_enabled || (!$phoneBlackList->pbl_expiration_date || !(strtotime($phoneBlackList->pbl_expiration_date) > time()))) {
                    $this->phoneBlackListManageService->enableWithExpiredDateTime($phoneBlackList, new \DateTime());
                } else {
                    $enableNotifier = false;
                }
            } else {
                $this->phoneBlackListManageService->add($phone, new \DateTime());
            }
        } catch (\RuntimeException $e) {
            return $this->asJson([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableLog($e, true), 'CallController::actionAjaxAddPhoneBlackList::Throwable');
            return $this->asJson([
                'error' => true,
                'message' => 'Internal server Error'
            ]);
        }

        return $this->asJson([
            'error' => false,
            'notifier' => $enableNotifier
        ]);
    }

    public function actionAllowList(): array
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            /** @abac CallAbacObject::ACT_DATA_ALLOW_LIST, CallAbacObject::ACTION_TOGGLE_DATA, Access to add/remove ContactPhoneData - key allow_list */
            if (!Yii::$app->abac->can(null, CallAbacObject::ACT_DATA_ALLOW_LIST, CallAbacObject::ACTION_TOGGLE_DATA)) {
                throw new ForbiddenHttpException('Access Denied');
            }

            $result = ['message' => '', 'status' => 0, 'result' => ''];
            $callId = (int) Yii::$app->request->post('call_id');

            try {
                if (!$call = Call::findOne($callId)) {
                    throw new \DomainException('Call not found');
                }

                $contactPhoneList = ContactPhoneListService::getOrCreate($call->c_from);
                if (ContactPhoneListService::isAllowList($call->c_from)) {
                    ContactPhoneDataService::removeByCplIdAndKey($contactPhoneList->cpl_id, ContactPhoneDataDictionary::KEY_ALLOW_LIST);
                    $result['result'] = 'removed';
                } else {
                    ContactPhoneDataService::getOrCreate(
                        $contactPhoneList->cpl_id,
                        ContactPhoneDataDictionary::KEY_ALLOW_LIST,
                        ContactPhoneDataDictionary::DEFAULT_TRUE_VALUE
                    );
                    $result['result'] = 'added';
                }

                $result['message'] = 'Success';
                $result['status'] = 1;
            } catch (\RuntimeException | \DomainException $exception) {
                Yii::warning(AppHelper::throwableLog($exception), 'CallController:actionAllowList::exception');
                $result['message'] = VarDumper::dumpAsString($exception->getMessage());
            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableLog($throwable), 'CallController:actionAllowList:throwable');
                $result['message'] = 'Internal Server Error';
            }
            return $result;
        }
        throw new BadRequestHttpException();
    }

    public function actionReconnect(): Response
    {
        $callSid = (string) \Yii::$app->request->post('sid');
        if (!$callSid) {
            return $this->asJson([
                'error' => true,
                'message' => 'Not found CallSid',
            ]);
        }

        $response = [
            'error' => false,
            'message' => 'Ok',
        ];

        try {
            $userId = Auth::id();
            $call = $this->callRepository->findBySid($callSid);
            if (!$call->isStatusInProgress()) {
                throw new \DomainException('Call status is invalid.');
            }
            if (!$call->isOwner($userId)) {
                throw new \DomainException('Is not your call.');
            }
            if (!$call->getDataCreatorType()->isAgent()) {
                throw new \DomainException('Call creator type is invalid.');
            }
            $prepare = new PrepareCurrentCallsForNewCall($userId);
            if (!$prepare->prepare(SettingHelper::getCallReconnectAnnounceMessage())) {
                throw new \DomainException('Some errors. Please try again latter.');
            }
        } catch (\Throwable $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        return $this->asJson($response);
    }

    private function addUsersForCall(Call $call, array $users): array
    {
        $result = [];
        if ($users) {
            foreach ($users as $userId) {
                if (Call::applyCallToAgentAccess($call, $userId)) {
                    $result[] = $userId;
                }
            }
        }
        return $result;
    }

    private function getAvailableUsers(Call $call, array $groups): array
    {
        $users = [];
        $onlineUsers = $this->getUsersOnlineForAddToCall($call, $groups);
        $diffUsers = array_diff(array_keys($onlineUsers), $this->getUsersAlreadyAccess($call));
        if ($diffUsers) {
            foreach ($diffUsers as $userId) {
                if (array_key_exists($userId, $onlineUsers)) {
                    $users[$userId] = $onlineUsers[$userId];
                }
            }
        }
        unset($onlineUsers);
        return $users;
    }

    private function getUsersAlreadyAccess(Call $call): array
    {
        return array_keys(CallUserAccess::find()
            ->select(['cua_user_id'])
            ->andWhere(['cua_call_id' => $call->c_id])->andWhere(['cua_status_id' => CallUserAccess::STATUS_TYPE_PENDING])
            ->indexBy('cua_user_id')
            ->column());
    }

    private function getUsersOnlineForAddToCall(Call $call, array $groups): array
    {
        $query = Employee::find()
            ->select(['*'])
            ->addSelect(['us_is_on_call'])
            ->leftJoin(UserStatus::tableName(), 'us_user_id = ' . Employee::tableName() . '.id')
            ->andWhere(['IN', 'id', UserConnection::find()->select(['uc_user_id'])->groupBy(['uc_user_id'])])
            ->andWhere(['IN', 'id', UserDepartment::find()->select(['DISTINCT(ud_user_id)'])->where(['ud_dep_id' => $call->c_dep_id])])
            ->andWhere(['IN', 'id', ProjectEmployeeAccess::find()->select(['DISTINCT(employee_id)'])->where(['project_id' => $call->c_project_id])])
            ->andWhere([
                'OR',
                ['IS', 'us_is_on_call', null],
                ['us_is_on_call' => false],
            ]);

        if ($groups) {
            $query->andWhere(['IN', 'id', UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['ugs_group_id' => $groups])]);
        }

        return $query->indexBy('id')->all();
    }

    protected function findCallModel(string $sid): Call
    {
        if (($model = Call::findOne(['c_call_sid' => $sid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findCallLogModel(string $sid): CallLog
    {
        if (($model = CallLog::findOne(['cl_call_sid' => $sid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
