<?php

namespace frontend\controllers;

use common\components\jobs\RocketChatUserUpdateJob;
use common\models\Employee;
use common\models\EmployeeAcl;
use common\models\EmployeeContactInfo;
use common\models\LoginForm;
use common\models\Project;
use common\models\ProjectEmployeeAccess;
use common\models\search\EmployeeSearch;
use common\models\search\UserProjectParamsSearch;
use common\models\UserDepartment;
use common\models\UserGroupAssign;
use common\models\UserParams;
use common\models\UserProductType;
use common\models\UserProfile;
use common\models\UserProjectParams;
use frontend\models\UserFailedLogin;
use frontend\models\UserMultipleForm;
use modules\shiftSchedule\src\abac\ShiftAbacObject;
use modules\shiftSchedule\src\entities\userShiftAssign\repository\UserShiftAssignRepository;
use modules\user\src\update\UpdateForm;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use src\model\clientChatUserChannel\entity\ClientChatUserChannel;
use src\model\emailList\entity\EmailList;
use modules\shiftSchedule\src\entities\userShiftAssign\UserShiftAssign;
use src\model\userClientChatData\entity\UserClientChatData;
use src\model\userClientChatData\entity\UserClientChatDataScopes;
use src\model\userClientChatData\service\UserClientChatDataService;
use src\model\userVoiceMail\entity\search\UserVoiceMailSearch;
use src\repositories\clientChatUserChannel\ClientChatUserChannelRepository;
use src\services\clientChat\ClientChatRequesterService;
use src\services\clientChatMessage\ClientChatMessageService;
use src\services\clientChatUserAccessService\ClientChatUserAccessService;
use Yii;
use yii\bootstrap4\Html;
use yii\caching\TagDependency;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * EmployeeController controller
 *
 * @property ClientChatUserAccessService $clientChatUserAccessService
 * @property ClientChatMessageService $clientChatMessageService
 * @property ClientChatUserChannelRepository $clientChatUserChannelRepository
 * @property UserClientChatDataService $userClientChatDataService
 */
class EmployeeController extends FController
{
    /**
     * @var ClientChatUserAccessService
     */
    private ClientChatUserAccessService $clientChatUserAccessService;
    /**
     * @var ClientChatMessageService
     */
    private ClientChatMessageService $clientChatMessageService;
    /**
     * @var ClientChatUserChannelRepository
     */
    private ClientChatUserChannelRepository $clientChatUserChannelRepository;
    /**
     * @var UserClientChatDataService
     */
    private UserClientChatDataService $userClientChatDataService;

    /**
     * @return array
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

    public function __construct(
        $id,
        $module,
        ClientChatUserAccessService $clientChatUserAccessService,
        ClientChatMessageService $clientChatMessageService,
        ClientChatUserChannelRepository $clientChatUserChannelRepository,
        UserClientChatDataService $userClientChatDataService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->clientChatUserAccessService = $clientChatUserAccessService;
        $this->clientChatMessageService = $clientChatMessageService;
        $this->clientChatUserChannelRepository = $clientChatUserChannelRepository;
        $this->userClientChatDataService = $userClientChatDataService;
    }

    public function actionSellerContactInfo($employeeId)
    {
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $roles = $user->getRoles();

        if (is_array($roles)) {
            $roles = array_keys($roles);
        }

        //print_r($roles); exit;

        if (empty($roles)) {
            throw new ForbiddenHttpException('Not found roles');
        } elseif (!$user->isAdmin() && $user->id != $employeeId) {
            throw new ForbiddenHttpException('AccessDenied (' . $employeeId . ')');
        }

        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $result = [
                'success' => true,
                'errors' => []
            ];
            $errors = [];
            $attrArr = Yii::$app->request->post('EmployeeContactInfo');



            foreach ($attrArr as $key => $attr) {
                $model = empty($attr['id']) ? null : EmployeeContactInfo::findOne(['id' => $attr['id']]);

                if ($model === null) {
                    $model = new EmployeeContactInfo();
                }
                $model->attributes = $attr;



                if ($model->needSave()) {
                    if (!$model->save()) {
                        //print_r($model->errors); exit;

                        if ($model->hasErrors('email_user')) {
                            $errors[Html::getInputId($model, '[' . $key . ']email_user')] = true;
                        }
                        if ($model->hasErrors('email_pass')) {
                            $errors[Html::getInputId($model, '[' . $key . ']email_pass')] = true;
                        }
                        if ($model->hasErrors('direct_line')) {
                            $errors[Html::getInputId($model, '[' . $key . ']direct_line')] = true;
                        }

                        $errors[$key] = VarDumper::dumpAsString($model->getFirstErrors());
                    }
                }
            }

            if ($errors) {
                $result['success'] = false;
                $result['errors'] = $errors;
            }
            return $result;
        }

        return null;
    }

    public function actionAclRule($id = 0)
    {
        if (empty($id)) {
            $model = new EmployeeAcl();
        } else {
            $model = EmployeeAcl::findOne(['id' => $id]);
        }

        if (Yii::$app->request->isPost) {
            $attr = Yii::$app->request->post($model->formName());
            $model->attributes = $attr;
            if ($model->isNewRecord) {
                $success = $model->save();
                Yii::$app->response->format = Response::FORMAT_JSON;
                $employee = Employee::findOne($model->employee_id);
                return [
                    'body' => $this->renderAjax('partial/_aclList', [
                        'models' => $employee->employeeAcl,
                    ]),
                    'success' => $success
                ];
            } else {
                return $model->save();
            }
        }

        return null;
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionList()
    {
        $multipleForm = new UserMultipleForm();
        $multipleErrors = [];

        if ($multipleForm->load(Yii::$app->request->post()) && $multipleForm->validate()) {
            //VarDumper::dump(json_decode($multipleForm->user_list_json)); exit;
            //VarDumper::dump($multipleForm->user_list); exit;
            //var_dump($multipleForm->timeZone); die();
            if (\is_array($multipleForm->user_list)) {
                foreach ($multipleForm->user_list as $user_id) {
                    $user_id = (int) $user_id;
                    $user = Employee::findOne($user_id);

                    if ($user) {
                        if (!$uParams = $user->userParams) {
                            Yii::error('User Id: ' . $user->id . ' Error. Please create UserParams for this user.', 'Employee:list:multipleUpdate');
                            $multipleErrors[$user_id][] = 'User Id:' . $user->id . ' Error. Please create UserParams for this user.';
                            continue;
                        }

                        $uParamsNeedSave = false;
                        $uProfileNeedSave = false;

                        if (!$uProfile = $user->userProfile) {
                            $uProfile = new UserProfile();
                            $uProfile->up_user_id = $user->id;
                            $uProfileNeedSave = true;
                        }

                        if (is_numeric($multipleForm->up_call_expert_limit)) {
                            $uParams->up_call_expert_limit = (int) $multipleForm->up_call_expert_limit;
                            $uParamsNeedSave = true;
                        }

                        if ($multipleForm->workStart != "") {
                            $uParams->up_work_start_tm = $multipleForm->workStart . ':00';
                            $uParamsNeedSave = true;
                        }

                        if ($multipleForm->timeZone != "") {
                            $uParams->up_timezone = $multipleForm->timeZone;
                            $uParamsNeedSave = true;
                        }

                        if (is_numeric($multipleForm->workMinutes)) {
                            $uParams->up_work_minutes = (int)$multipleForm->workMinutes;
                            $uParamsNeedSave = true;
                        }

                        if (is_numeric($multipleForm->inboxShowLimitLeads)) {
                            $uParams->up_inbox_show_limit_leads = (int)$multipleForm->inboxShowLimitLeads;
                            $uParamsNeedSave = true;
                        }

                        if (is_numeric($multipleForm->defaultTakeLimitLeads)) {
                            $uParams->up_default_take_limit_leads = (int)$multipleForm->defaultTakeLimitLeads;
                            $uParamsNeedSave = true;
                        }

                        if (is_numeric($multipleForm->minPercentForTakeLeads)) {
                            $uParams->up_min_percent_for_take_leads = (int)$multipleForm->minPercentForTakeLeads;
                            $uParamsNeedSave = true;
                        }

                        if (is_numeric($multipleForm->frequencyMinutes)) {
                            $uParams->up_frequency_minutes = (int)$multipleForm->frequencyMinutes;
                            $uParamsNeedSave = true;
                        }

                        if (is_numeric($multipleForm->baseAmount)) {
                            $uParams->up_base_amount = $multipleForm->baseAmount;
                            $uParamsNeedSave = true;
                        }

                        if (is_numeric($multipleForm->autoRedial)) {
                            $uProfile->up_auto_redial = $multipleForm->autoRedial;
                            $uProfileNeedSave = true;
                        }

                        if (is_numeric($multipleForm->kpiEnable)) {
                            $uProfile->up_kpi_enable = $multipleForm->kpiEnable;
                            $uProfileNeedSave = true;
                        }

                        if (is_numeric($multipleForm->leaderBoardEnabled)) {
                            $uParams->up_leaderboard_enabled = $multipleForm->leaderBoardEnabled;
                            $uParamsNeedSave = true;
                        }

                        if (is_numeric($multipleForm->commissionPercent)) {
                            $uParams->up_commission_percent = $multipleForm->commissionPercent;
                            $uParamsNeedSave = true;
                        }

                        if ($multipleForm->userDepartments) {
                            $transaction = Yii::$app->db->beginTransaction();
                            try {
                                $user->removeAllDepartments();
                                $user->addNewDepartments($multipleForm->userDepartments);
                                $transaction->commit();
                            } catch (\Throwable $e) {
                                $transaction->rollBack();
                                Yii::error($e->getMessage(), 'Employee:list:multipleUpdate:userDepartments');
                                $multipleErrors[$user_id][] = $e->getMessage();
                            }
                        }

                        if ($multipleForm->userClientChatChanels) {
                            $transaction = Yii::$app->db->beginTransaction();
                            try {
                                $user->removeAllClientChatChanels();
                                $user->addClientChatChanels($multipleForm->userClientChatChanels, Auth::id());
//                                $this->clientChatUserAccessService->disableUserAccessToAllChats($user->id);
                                $this->clientChatUserAccessService->setUserAccessToAllChatsByChannelIds($multipleForm->userClientChatChanels, $user->id);
                                $transaction->commit();
                            } catch (\Throwable $e) {
                                $transaction->rollBack();
                                Yii::error($e->getMessage(), 'Employee:list:multipleUpdate:clientChatChanels');
                                $multipleErrors[$user_id][] = $e->getMessage();
                            }
                        }

                        if ($uParamsNeedSave && !$uParams->save()) {
                            Yii::error(VarDumper::dumpAsString($uParams->getErrors()), 'Employee:list:multipleUpdate:userParams:save');
                            $multipleErrors[$user_id][] = $uParams->getErrors();
                        }

                        if ($uProfileNeedSave && !$uProfile->save()) {
                            Yii::error(VarDumper::dumpAsString($uProfile->getErrors()), 'Employee:list:multipleUpdate:userProfile:save');
                            $multipleErrors[$user_id][] = $uProfile->getErrors();
                        }

                        if ($multipleForm->userRoles) {
                            $transaction = Yii::$app->db->beginTransaction();
                            try {
                                $user->removeAllRoles();
                                $user->addNewRoles($multipleForm->userRoles);
                                $transaction->commit();
                            } catch (\Throwable $e) {
                                $transaction->rollBack();
                                Yii::error($e->getMessage(), 'Employee:list:multipleUpdate:userRoles');
                                $multipleErrors[$user_id][] = $e->getMessage();
                            }
                        }

                        if (is_numeric($multipleForm->status)) {
                            $user->status = $multipleForm->status;
                            $user->form_roles = ArrayHelper::map(Yii::$app->authManager->getRolesByUser($user->id), 'name', 'name');
                            if (!$user->save()) {
                                Yii::error(VarDumper::dumpAsString($user->errors), 'Employee:list:multipleupdate:userParams:save');
                                $multipleErrors[$user_id][] = $user->getErrors();
                            }
                        }
                    }
                }
            }
        }

        $searchModel = new EmployeeSearch();
        $params = Yii::$app->request->queryParams;

        /** @var Employee $auth */
        $auth = Yii::$app->user->identity;

        if ($auth->isSupervision()) {
            $params['EmployeeSearch']['supervision_id'] = $auth->id;
        }

        if (Yii::$app->request->get('act') === 'select-all') {
            $data = $searchModel->searchIds(Yii::$app->request->queryParams);
            return $this->asJson($data);
        }

        $dataProvider = $searchModel->search($params);

        return $this->render('list', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'multipleForm' => $multipleForm,
            'multipleErrors' => $multipleErrors,
        ]);
    }

    /**
     * @return string|Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        $model = new Employee(['scenario' => Employee::SCENARIO_REGISTER]);
        $modelUserParams = new UserParams();
        $modelProfile = new UserProfile();

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $attr = Yii::$app->request->post($model->formName());

            //VarDumper::dump($model->make_user_project_params, 10, true); exit;

            $model->prepareSave($attr);

            $transaction = Yii::$app->db->beginTransaction();

            try {
                if ($model->save()) {
                    //if (Auth::user()->isAdmin()) {
                        $modelProfile->up_user_id = $model->id;
                    if ($modelProfile->load(Yii::$app->request->post())) {
                        $modelProfile->up_updated_dt = date('Y-m-d H:i:s');
                        if (!$modelProfile->save()) {
                            Yii::error(VarDumper::dumpAsString($modelProfile->errors), 'EmployeeController:actionCreate:modelProfile:save');
                            throw new \Exception('Profile settings error');
                        }
                    } else {
                        throw new \Exception('Profile settings is empty');
                    }
                    //}

                    // VarDumper::dump($model->form_roles, 10, true); exit;

                    if ($model->form_roles) {
                        $availableRoles = Employee::getAllRoles(Auth::user());
                        foreach ($model->form_roles as $keyItem => $roleItem) {
                            if (!array_key_exists($roleItem, $availableRoles)) {
                                unset($model->form_roles[$keyItem]);
                            }
                        }
                    }

                    $model->addRole(true);

                    if (!\Yii::$app->authManager->getRolesByUser($model->id)) {
                        throw new \Exception('Roles is empty');
                    }

                    if (isset($attr['user_groups'])) {
                        if ($attr['user_groups']) {
                            foreach ($attr['user_groups'] as $ugId) {
                                $uga = new UserGroupAssign();
                                $uga->ugs_user_id = $model->id;
                                $uga->ugs_group_id = (int)$ugId;
                                if (!$uga->save()) {
                                    Yii::error(VarDumper::dumpAsString($uga->getErrors()), 'Employee:Create:UserGroupAssign:save');
                                    throw new \Exception('User groups error. ' . VarDumper::dumpAsString($uga->getErrors()));
                                }
                            }
                        }
                    }


                    if (isset($attr['user_departments'])) {
                        if ($attr['user_departments']) {
                            foreach ($attr['user_departments'] as $udId) {
                                $ud = new UserDepartment();
                                $ud->ud_user_id = $model->id;
                                $ud->ud_dep_id = (int)$udId;
                                if (!$ud->save()) {
                                    Yii::error(VarDumper::dumpAsString($ud->getErrors()), 'Employee:Create:UserDepartment:save');
                                    throw new \Exception('User Department error. ' . VarDumper::dumpAsString($ud->getErrors()));
                                }
                            }
                        }
                    }


                    if (isset($attr['user_projects'])) {
                        if ($attr['user_projects']) {
                            foreach ($attr['user_projects'] as $ugId) {
                                $up = new ProjectEmployeeAccess();
                                $up->employee_id = $model->id;
                                $up->project_id = (int)$ugId;
                                $up->created = date('Y-m-d H:i:s');
                                if (!$up->save()) {
                                    Yii::error(VarDumper::dumpAsString($up->getErrors()), 'Employee:Create:ProjectEmployeeAccess:save');
                                    throw new \Exception('Project Access error. ' . VarDumper::dumpAsString($up->getErrors()));
                                }
                            }
                        }
                    }


                    $emailArr = explode('@', $model->email);
                    $emailPrefix = $emailArr[0] ?? null;

                    //VarDumper::dump($emailPrefix, 10, true);

                    if ($model->make_user_project_params) {
                        if (!empty($attr['user_projects'])) {
                            foreach ($attr['user_projects'] as $projectId) {
                                //VarDumper::dump($projectId, 10, true);

                                $project = Project::findOne($projectId);
                                if (!$project || $project->closed) {
                                    continue;
                                }


                                $emailId = null;
                                if ($emailPrefix && $project->email_postfix) {
                                    $email = new EmailList();
                                    $email->el_email = $emailPrefix . '@' . $project->email_postfix;
                                    $email->el_title = $project->name . ' - ' . $model->username;
                                    $email->el_enabled = true;

                                    if ($email->save()) {
                                        $emailId = $email->el_id;
                                    } else {
                                        Yii::error(VarDumper::dumpAsString([$email->attributes, $email->errors]), 'Employee:Create:EmailList:save');
                                        throw new \Exception('EmailList error. ' . VarDumper::dumpAsString($email->getErrors()));
                                    }
                                }

                                //VarDumper::dump('EmId:' . $emailId, 10, true);

                                $upp = new UserProjectParams();
                                $upp->upp_user_id = $model->id;
                                $upp->upp_project_id = (int)$projectId;
                                $upp->upp_created_dt = date('Y-m-d H:i:s');
                                if ($emailId) {
                                    $upp->upp_email_list_id = $emailId;
                                }
                                if (!$upp->save()) {
                                    Yii::error(VarDumper::dumpAsString([$upp->attributes, $upp->errors]), 'Employee:Create:UserProjectParams:save');
                                    throw new \Exception('Project Params error. ' . VarDumper::dumpAsString($upp->getErrors()));
                                }
                            }
                        }
                    }

                    if ($modelUserParams->load(Yii::$app->request->post())) {
                        $modelUserParams->up_user_id = $model->id;
                        $modelUserParams->up_updated_user_id = Yii::$app->user->id;
                        if (!$modelUserParams->save()) {
                            Yii::error(VarDumper::dumpAsString($modelUserParams->getErrors()), 'Employee:Create:modelUserParams:save');
                            throw new \Exception('User Params error. ' . VarDumper::dumpAsString($modelUserParams->getErrors()));
                        }
                    } else {
                        throw new \Exception('User Params is empty');
                    }

                    if ($modelUserParams->up_timezone == null) {
                        $modelUserParams->up_user_id = $model->id;
                        $modelUserParams->up_updated_user_id = Yii::$app->user->id;

                        $modelUserParams->up_timezone = "Europe/Chisinau";
                        $modelUserParams->up_work_minutes = 8 * 60;
                        $modelUserParams->up_base_amount = 0;
                        $modelUserParams->up_commission_percent = 0;
                        $modelUserParams->up_work_start_tm = "16:00";

                        if (!$modelUserParams->save()) {
                            Yii::error(VarDumper::dumpAsString($modelUserParams->getErrors()), 'Employee:Create:modelUserParams:timeZone:save');
                            throw new \Exception('User Params error. ' . VarDumper::dumpAsString($modelUserParams->getErrors()));
                        }
                    }

                    if (isset($attr['client_chat_user_channel'])) {
                        if ($attr['client_chat_user_channel']) {
                            foreach ($attr['client_chat_user_channel'] as $chId) {
                                $clientChatChanel = new ClientChatUserChannel();
                                $clientChatChanel->ccuc_user_id = $model->id;
                                $clientChatChanel->ccuc_channel_id = (int)$chId;
                                $clientChatChanel->ccuc_created_dt = date('Y-m-d H:i:s');
                                $clientChatChanel->ccuc_created_user_id = Auth::id();
                                $clientChatChanel->save();
                            }
//                            $this->clientChatUserAccessService->setUserAccessToAllChatsByChannelIds($attr['client_chat_user_channel'], $model->id);
                        }
                    }

                    $transaction->commit();

                    try {
                        $this->userClientChatDataService->createAndRegisterRcProfile(
                            UserClientChatData::generateUsername($model->id),
                            $model->nickname,
                            $model->email,
                            $model->id
                        );
                    } catch (\RuntimeException $e) {
                        Yii::$app->getSession()->setFlash('warning', 'RocketChat profile was not created: ' . $e->getMessage());
                    } catch (\Throwable $e) {
                        Yii::$app->getSession()->setFlash('warning', 'RocketChat profile was not created: Internal Server Error');
                        Yii::error(AppHelper::throwableLog($e, true), 'EmployeeController::actionCreate::createAndRegisterRcProfile::Throwable');
                    }

                    Yii::$app->getSession()->setFlash('success', 'User created');
                    return $this->redirect(['update', 'id' => $model->id]);
                }
                $transaction->rollBack();
            } catch (\Throwable $e) {
                $transaction->rollBack();
                $model->id = 0;
                $model->setIsNewRecord(true);
                Yii::$app->session->addFlash('error', $e->getMessage());
            }
        } else {
            $modelUserParams->up_timezone = 'Europe/Chisinau';
            $modelUserParams->up_work_minutes = 8 * 60;
            $modelUserParams->up_base_amount = 0;
            $modelUserParams->up_commission_percent = 0;
            $model->form_roles = ArrayHelper::map(Yii::$app->authManager->getRolesByUser($model->id), 'name', 'name') ;
        }

        //VarDumper::dump($model->userGroupAssigns, 10 ,true); exit;

        //$model->user_groups = ArrayHelper::map($model->userGroupAssigns, 'ugs_group_id', 'ugs_group_id');
        //$model->user_projects = ArrayHelper::map($model->projects, 'id', 'id');

        //VarDumper::dump($model->user_projects, 10, true); exit;


        $dataProvider = null;

        return $this->render('_form', [
            'model' => $model,
            'modelUserParams' => $modelUserParams,
            'dataProvider' => $dataProvider,
            'modelProfile' => $modelProfile
        ]);
    }


    /**
     * @return string|Response
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function actionUpdate()
    {

        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        if ($id = Yii::$app->request->get('id')) {
            $model = Employee::findOne($id);

            if (!$model) {
                throw new NotFoundHttpException('The requested user does not exist.');
            }


            if (!$user->isSuperAdmin()) {
                if ($model->isSuperAdmin()) {
                    throw new NotFoundHttpException('Access denied for Superadmin user: ' . $model->id);
                }
            }

            if ($user->isUserManager()) {
                if ($model->isOnlyAdmin()) {
                    throw new NotFoundHttpException('Access denied for Admin user: ' . $model->id);
                }
            }

            /*if ($user->isAnySupervision()) {
                if ($model->isAdmin()) {
                    throw new NotFoundHttpException('Access denied for Admin user: ' . $model->id);
                }
            }*/

            $modelUserParams = UserParams::findOne($model->id);
            if (!$modelUserParams) {
                $modelUserParams = new UserParams();
            }



            if ($user->isSupervision()) {
                $access = false;

                $userGroups = array_keys($model->getUserGroupList());

                foreach (Yii::$app->user->identity->getUserGroupList() as $grId => $grName) {
                    if (in_array($grId, $userGroups)) {
                        $access = true;
                        break;
                    }
                }

                if (!$access) {
                    throw new ForbiddenHttpException('Access denied for this user (invalid user group)');
                }
            }

            $modelProfile = $model->userProfile;
            //VarDumper::dump($modelProfile->attributes, 10, true); exit;
            if (!$modelProfile) {
                $modelProfile = new UserProfile();
                $modelProfile->up_user_id = $id;
                $modelProfile->up_join_date = date('Y-m-d');
            }
        } else {
            throw new BadRequestHttpException('Invalid request');
        }

//        /** @var Cache $cache */
//        $cache = Yii::$app->cache;
//        $userCache = new UserCache($model, $cache);
//        $userCache->flush();


        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $attr = Yii::$app->request->post($model->formName());

            $model->prepareSave($attr);

            $updateRC = [];
            if ($model->isAttributeChanged('email') && $model->validate(['email'])) {
                $updateRC['email'] = $model->email;
            }
            if ($model->isAttributeChanged('nickname') && $model->validate(['nickname'])) {
                $updateRC['name'] = $model->nickname;
            }

            if ($model->save()) {
                if ($model->form_roles) {
                    $availableRoles = Employee::getAllRoles(Auth::user());
                    foreach ($model->form_roles as $keyItem => $roleItem) {
                        if (!array_key_exists($roleItem, $availableRoles)) {
                            unset($model->form_roles[$keyItem]);
                        }
                    }
                }

                $model->addRole(false);

                //VarDumper::dump(Yii::$app->request->post(), 10, true); exit;

                if ($modelProfile->load(Yii::$app->request->post())) {
                    $modelProfile->up_updated_dt = date('Y-m-d H:i:s');

                    if (!$modelProfile->save()) {
                        Yii::error(VarDumper::dumpAsString($modelProfile->errors, 10), 'EmployeeController:actionUpdate:modelProfile:save');
                    }
                }

                if (isset($attr['user_groups'])) {
                    UserGroupAssign::deleteAll(['ugs_user_id' => $model->id]);
                    if ($attr['user_groups']) {
                        foreach ($attr['user_groups'] as $ugId) {
                            $uga = new UserGroupAssign();
                            $uga->ugs_user_id = $model->id;
                            $uga->ugs_group_id = (int) $ugId;
                            $uga->save();
                        }
                    }
                }


                if (isset($attr['user_departments'])) {
                    UserDepartment::deleteAll(['ud_user_id' => $model->id]);
                    if ($attr['user_departments']) {
                        foreach ($attr['user_departments'] as $udId) {
                            $ud = new UserDepartment();
                            $ud->ud_user_id = $model->id;
                            $ud->ud_dep_id = (int) $udId;
                            if (!$ud->save()) {
                                Yii::error(VarDumper::dumpAsString($ud->errors), 'Employee:Create:UserDepartment:save');
                            }
                        }
                    }
                }

                if (isset($attr['user_projects'])) {
                    ProjectEmployeeAccess::deleteAll(['employee_id' => $model->id]);
                    if ($attr['user_projects']) {
                        foreach ($attr['user_projects'] as $ugId) {
                            $up = new ProjectEmployeeAccess();
                            $up->employee_id = $model->id;
                            $up->project_id = (int) $ugId;
                            $up->created = date('Y-m-d H:i:s');
                            $up->save();
                        }
                    }
                }

                $userClientChatData = UserClientChatData::findOne(['uccd_employee_id' => $model->id]);

                if (isset($attr['client_chat_user_channel'])) {
                    ClientChatUserChannel::deleteAll(['ccuc_user_id' => $model->id]);
                    if ($attr['client_chat_user_channel']) {
                        foreach ($attr['client_chat_user_channel'] as $chId) {
                            $clientChatChanel = new ClientChatUserChannel();
                            $clientChatChanel->ccuc_user_id = $model->id;
                            $clientChatChanel->ccuc_channel_id = (int)$chId;
                            $clientChatChanel->ccuc_created_dt = date('Y-m-d H:i:s');
                            $clientChatChanel->ccuc_created_user_id = Auth::id();
                            $clientChatChanel->save();
                        }

                        if ($userClientChatData && $userClientChatData->isRegisteredInRc()) {
//                            $this->clientChatUserAccessService->disableUserAccessToAllChats($model->id);
                            $this->clientChatUserAccessService->setUserAccessToAllChatsByChannelIds($attr['client_chat_user_channel'], $model->id);
                        } else {
                            $this->clientChatUserAccessService->disableUserAccessToAllChats($model->id);
                        }
                    } else {
                        $this->clientChatUserAccessService->disableUserAccessToAllChats($model->id);
                    }
                    TagDependency::invalidate(Yii::$app->cache, ClientChatUserChannel::cacheTags($model->id));
                }

                /** @abac ShiftAbacObject::ACT_USER_SHIFT_ASSIGN, ShiftAbacObject::ACTION_UPDATE, Access edit UserShiftAssign */
                if (
                    isset($attr['user_shift_assigns']) &&
                    \Yii::$app->abac->can(null, ShiftAbacObject::ACT_USER_SHIFT_ASSIGN, ShiftAbacObject::ACTION_UPDATE)
                ) {
                    UserShiftAssign::deleteAll(['usa_user_id' => $model->id]);
                    if ($attr['user_shift_assigns']) {
                        foreach ($attr['user_shift_assigns'] as $shiftId) {
                            try {
                                $userShiftAssign = UserShiftAssign::create($model->id, $shiftId);
                                (new UserShiftAssignRepository($userShiftAssign))->save(true);
                            } catch (\RuntimeException | \DomainException $throwable) {
                                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), [
                                    'userId' => $model->id, 'shiftId' => $shiftId,
                                ]);
                                \Yii::warning($message, 'EmployeeController:actionUpdate:Exception');
                                \Yii::$app->getSession()->setFlash('warning', $throwable->getMessage());
                            } catch (\Throwable $throwable) {
                                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), [
                                    'userId' => $model->id, 'shiftId' => $shiftId,
                                ]);
                                \Yii::error($message, 'EmployeeController:actionUpdate:Throwable');
                                \Yii::$app->getSession()->setFlash('danger', 'UserShiftAssign not saved');
                            }
                        }
                    }
                }

                if (!empty($updateRC) && $userClientChatData && $userClientChatData->isRegisteredInRc()) {
                    $job = new RocketChatUserUpdateJob();
                    $job->userId = $userClientChatData->getRcUserId();
                    $job->data = $updateRC;
                    $job->userClientChatDataId = $userClientChatData->uccd_id;
                    Yii::$app->queue_job->priority(10)->push($job);
                }

                Yii::$app->getSession()->setFlash('success', 'User updated');
            }
        } else {
            $model->form_roles = ArrayHelper::map(Yii::$app->authManager->getRolesByUser($model->id), 'name', 'name') ;
        }

        //VarDumper::dump($model->userGroupAssigns, 10 ,true); exit;

        $model->user_groups = ArrayHelper::map($model->userGroupAssigns, 'ugs_group_id', 'ugs_group_id');
        $model->user_projects = ArrayHelper::map($model->projects, 'id', 'id');
        $model->user_departments = ArrayHelper::map($model->userDepartments, 'ud_dep_id', 'ud_dep_id');
        $model->client_chat_user_channel = ArrayHelper::map($model->clientChatUserChannel, 'ccuc_channel_id', 'ccuc_channel_id');
        $model->user_shift_assigns = ArrayHelper::map($model->userShiftAssigns, 'usa_sh_id', 'usa_sh_id');

        $searchModel = new UserProjectParamsSearch();
        $params = Yii::$app->request->queryParams;

        $params['UserProjectParamsSearch']['upp_user_id'] = $model->id;

        $dataProvider = $searchModel->search($params);

        if ($modelUserParams->load(Yii::$app->request->post())) {
            $modelUserParams->up_user_id = $model->id;
            $modelUserParams->up_updated_user_id = Yii::$app->user->id;

            if ($modelUserParams->save()) {
                return $this->refresh();
            }
        }

        $dataLastFailedLogin = new ActiveDataProvider([
            'query' => UserFailedLogin::find()->andFilterWhere(['ufl_user_id' => $model->id]),
            'sort' => ['defaultOrder' => ['ufl_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $result = [
            'model' => $model,
            'modelUserParams' => $modelUserParams,
            'dataProvider' => $dataProvider,
            'modelProfile' => $modelProfile,
            'lastFailedLoginAttempts' => $dataLastFailedLogin,
        ];

        $userVoiceMailSearch = new UserVoiceMailSearch();
        $result['userVoiceMailProvider'] = $userVoiceMailSearch->search(['UserVoiceMailSearch' => ['uvm_user_id' => $model->id]]);

        if (Auth::can('user-product-type/list')) {
            $dataUserProductType = new ActiveDataProvider([
                'query' => UserProductType::find()->andFilterWhere(['upt_user_id' => $model->id])
            ]);
            $result = ArrayHelper::merge($result, ['dataUserProductType' => $dataUserProductType]);
        }

        return $this->render('_form', $result);
    }

    public function actionUpdate2()
    {
        $targetUserId = (int)Yii::$app->request->get('id');
        if (!$targetUserId) {
            throw new BadRequestHttpException('Invalid request');
        }
        $targetUser = Employee::findOne($targetUserId);

        if (!$targetUser) {
            throw new NotFoundHttpException('The requested user does not exist.');
        }

        $updaterUser = Auth::user();

        if ($targetUser->isSuperAdmin() && !$updaterUser->isSuperAdmin()) {
            throw new ForbiddenHttpException('Access denied for Superadmin user' . $targetUser->id);
        }

        if ($targetUser->isOnlyAdmin() && $updaterUser->isUserManager()) {
            throw new ForbiddenHttpException('Access denied for Admin user: ' . $targetUser->id);
        }

        if ($updaterUser->isSupervision()) {
            if (!$updaterUser->isUserGroupIntersection(array_keys($targetUser->getUserGroupList()))) {
                throw new ForbiddenHttpException('Access denied for this user (invalid user group)');
            }
        }

        $form = new UpdateForm($targetUser, $updaterUser);

//        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
//            $attr = Yii::$app->request->post($model->formName());
//
//            $model->prepareSave($attr);
//
//            $updateRC = [];
//            if ($model->isAttributeChanged('email') && $model->validate(['email'])) {
//                $updateRC['email'] = $model->email;
//            }
//            if ($model->isAttributeChanged('nickname') && $model->validate(['nickname'])) {
//                $updateRC['name'] = $model->nickname;
//            }
//
//            if ($model->save()) {
//                if ($model->form_roles) {
//                    $availableRoles = Employee::getAllRoles(Auth::user());
//                    foreach ($model->form_roles as $keyItem => $roleItem) {
//                        if (!array_key_exists($roleItem, $availableRoles)) {
//                            unset($model->form_roles[$keyItem]);
//                        }
//                    }
//                }
//
//                $model->addRole(false);
//
//                if ($profile->load(Yii::$app->request->post())) {
//                    $profile->up_updated_dt = date('Y-m-d H:i:s');
//
//                    if (!$profile->save()) {
//                        Yii::error(VarDumper::dumpAsString($profile->errors, 10), 'EmployeeController:actionUpdate:modelProfile:save');
//                    }
//                }
//
//                if (isset($attr['user_groups'])) {
//                    UserGroupAssign::deleteAll(['ugs_user_id' => $model->id]);
//                    if ($attr['user_groups']) {
//                        foreach ($attr['user_groups'] as $ugId) {
//                            $uga = new UserGroupAssign();
//                            $uga->ugs_user_id = $model->id;
//                            $uga->ugs_group_id = (int) $ugId;
//                            $uga->save();
//                        }
//                    }
//                }
//
//
//                if (isset($attr['user_departments'])) {
//                    UserDepartment::deleteAll(['ud_user_id' => $model->id]);
//                    if ($attr['user_departments']) {
//                        foreach ($attr['user_departments'] as $udId) {
//                            $ud = new UserDepartment();
//                            $ud->ud_user_id = $model->id;
//                            $ud->ud_dep_id = (int) $udId;
//                            if (!$ud->save()) {
//                                Yii::error(VarDumper::dumpAsString($ud->errors), 'Employee:Create:UserDepartment:save');
//                            }
//                        }
//                    }
//                }
//
//                if (isset($attr['user_projects'])) {
//                    ProjectEmployeeAccess::deleteAll(['employee_id' => $model->id]);
//                    if ($attr['user_projects']) {
//                        foreach ($attr['user_projects'] as $ugId) {
//                            $up = new ProjectEmployeeAccess();
//                            $up->employee_id = $model->id;
//                            $up->project_id = (int) $ugId;
//                            $up->created = date('Y-m-d H:i:s');
//                            $up->save();
//                        }
//                    }
//                }
//
//                $userClientChatData = UserClientChatData::findOne(['uccd_employee_id' => $model->id]);
//
//                if (isset($attr['client_chat_user_channel'])) {
//                    ClientChatUserChannel::deleteAll(['ccuc_user_id' => $model->id]);
//                    if ($attr['client_chat_user_channel']) {
//                        foreach ($attr['client_chat_user_channel'] as $chId) {
//                            $clientChatChanel = new ClientChatUserChannel();
//                            $clientChatChanel->ccuc_user_id = $model->id;
//                            $clientChatChanel->ccuc_channel_id = (int)$chId;
//                            $clientChatChanel->ccuc_created_dt = date('Y-m-d H:i:s');
//                            $clientChatChanel->ccuc_created_user_id = Auth::id();
//                            $clientChatChanel->save();
//                        }
//
//                        if ($userClientChatData && $userClientChatData->isRegisteredInRc()) {
//                            $this->clientChatUserAccessService->setUserAccessToAllChatsByChannelIds($attr['client_chat_user_channel'], $model->id);
//                        } else {
//                            $this->clientChatUserAccessService->disableUserAccessToAllChats($model->id);
//                        }
//                    } else {
//                        $this->clientChatUserAccessService->disableUserAccessToAllChats($model->id);
//                    }
//                    TagDependency::invalidate(Yii::$app->cache, ClientChatUserChannel::cacheTags($model->id));
//                }
//
//                /** @abac ShiftAbacObject::ACT_USER_SHIFT_ASSIGN, ShiftAbacObject::ACTION_UPDATE, Access edit UserShiftAssign */
//                if (
//                    isset($attr['user_shift_assigns']) &&
//                    \Yii::$app->abac->can(null, ShiftAbacObject::ACT_USER_SHIFT_ASSIGN, ShiftAbacObject::ACTION_UPDATE)
//                ) {
//                    UserShiftAssign::deleteAll(['usa_user_id' => $model->id]);
//                    if ($attr['user_shift_assigns']) {
//                        foreach ($attr['user_shift_assigns'] as $shiftId) {
//                            try {
//                                $userShiftAssign = UserShiftAssign::create($model->id, $shiftId);
//                                (new UserShiftAssignRepository($userShiftAssign))->save(true);
//                            } catch (\RuntimeException | \DomainException $throwable) {
//                                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), [
//                                    'userId' => $model->id, 'shiftId' => $shiftId,
//                                ]);
//                                \Yii::warning($message, 'EmployeeController:actionUpdate:Exception');
//                                \Yii::$app->getSession()->setFlash('warning', $throwable->getMessage());
//                            } catch (\Throwable $throwable) {
//                                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), [
//                                    'userId' => $model->id, 'shiftId' => $shiftId,
//                                ]);
//                                \Yii::error($message, 'EmployeeController:actionUpdate:Throwable');
//                                \Yii::$app->getSession()->setFlash('danger', 'UserShiftAssign not saved');
//                            }
//                        }
//                    }
//                }
//
//                if (!empty($updateRC) && $userClientChatData && $userClientChatData->isRegisteredInRc()) {
//                    $job = new RocketChatUserUpdateJob();
//                    $job->userId = $userClientChatData->getRcUserId();
//                    $job->data = $updateRC;
//                    $job->userClientChatDataId = $userClientChatData->uccd_id;
//                    Yii::$app->queue_job->priority(10)->push($job);
//                }
//
//                Yii::$app->getSession()->setFlash('success', 'User updated');
//            }
//        } else {
//            $model->form_roles = ArrayHelper::map(Yii::$app->authManager->getRolesByUser($model->id), 'name', 'name') ;
//        }

        $userProjectParamsSearch = new UserProjectParamsSearch();
        $params = Yii::$app->request->queryParams;
        $params['UserProjectParamsSearch']['upp_user_id'] = $targetUser->id;
        $userProjectParamsDataProvider = $userProjectParamsSearch->search($params);

        $dataLastFailedLoginDataProvider = new ActiveDataProvider([
            'query' => UserFailedLogin::find()->andFilterWhere(['ufl_user_id' => $targetUser->id]),
            'sort' => ['defaultOrder' => ['ufl_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $result = [
            'form' => $form,
            'userProjectParamsDataProvider' => $userProjectParamsDataProvider,
            'dataLastFailedLoginDataProvider' => $dataLastFailedLoginDataProvider,
        ];

        $userVoiceMailSearch = new UserVoiceMailSearch();
        $result['userVoiceMailProvider'] = $userVoiceMailSearch->search(['UserVoiceMailSearch' => ['uvm_user_id' => $targetUser->id]]);

        if (Auth::can('user-product-type/list')) {
            $dataUserProductTypeDataProvider = new ActiveDataProvider([
                'query' => UserProductType::find()->andFilterWhere(['upt_user_id' => $targetUser->id])
            ]);
            $result = ArrayHelper::merge($result, ['dataUserProductTypeDataProvider' => $dataUserProductTypeDataProvider]);
        }

        return $this->render('update/_form', $result);
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionEmployeeValidation(): array
    {
        $id = Yii::$app->request->get('id');
        $model = ($id) ? Employee::findOne($id) : new Employee(['scenario' => Employee::SCENARIO_REGISTER]);

        if (Yii::$app->request->isAjax && $model && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        throw new BadRequestHttpException();
    }

    public function actionSwitch()
    {
        $user_id = Yii::$app->request->get('id');
        $user = Employee::findOne($user_id);
        if ($user) {
            if ($user->isOnlyAdmin() || $user->isSuperAdmin()) {
                return $this->redirect(['site/index']);
            }

            if (Yii::$app->user->login($user)) {
                LoginForm::sendWsIdentityCookie(Yii::$app->user->identity, 0);
            } else {
                echo 'Not logined';
                exit;
            }
            //$this->redirect(['site/index']);
        }
        return $this->redirect(['site/index']);
    }

    /**
     * @param string|null $q
     * @param int|null $id
     * @return array
     */
    public function actionListAjax(?string $q = null, ?int $id = null): array
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $out = ['results' => ['id' => '', 'text' => '', 'selection' => '']];

        if ($q !== null) {
            $query = Employee::find();
            $data = $query->select(['id', 'text' => 'username'])
                ->where(['like', 'username', $q])
                ->orWhere(['id' => (int) $q])
                ->limit(20)
                //->indexBy('id')
                ->asArray()
                ->all();

            if ($data) {
                foreach ($data as $n => $item) {
                    $text = $item['text'] . ' (' . $item['id'] . ')';
                    $data[$n]['text'] = self::formatText($text, $q);
                    $data[$n]['selection'] = $item['text'];
                }
            }

            $out['results'] = $data; //array_values($data);
        } elseif ($id > 0) {
            $user = Employee::findOne($id);
            $out['results'] = ['id' => $id, 'text' => $user ? $user->username : '', 'selection' => $user ? $user->username : ''];
        }
        return $out;
    }

    /**
     * @param string $str
     * @param string $term
     * @return string
     */
    public static function formatText(string $str, string $term): string
    {
        return preg_replace('~' . $term . '~i', '<b style="color: #e15554"><u>$0</u></b>', $str);
    }
}
