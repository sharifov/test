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
use sales\auth\Auth;
use sales\helpers\app\AppHelper;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use sales\model\emailList\entity\EmailList;
use sales\model\userVoiceMail\entity\search\UserVoiceMailSearch;
use sales\repositories\clientChatUserChannel\ClientChatUserChannelRepository;
use sales\services\clientChatMessage\ClientChatMessageService;
use sales\services\clientChatUserAccessService\ClientChatUserAccessService;
use Yii;
use yii\bootstrap4\Html;
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

    public function __construct($id, $module, ClientChatUserAccessService $clientChatUserAccessService, ClientChatMessageService $clientChatMessageService, ClientChatUserChannelRepository $clientChatUserChannelRepository, $config = [])
	{
		parent::__construct($id, $module, $config);
		$this->clientChatUserAccessService = $clientChatUserAccessService;
		$this->clientChatMessageService = $clientChatMessageService;
		$this->clientChatUserChannelRepository = $clientChatUserChannelRepository;
	}

    public function actionSellerContactInfo($employeeId)
    {
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $roles = $user->getRoles();

        if(is_array($roles)) {
            $roles = array_keys($roles);
        }

        //print_r($roles); exit;

        if (empty($roles)) {
            throw new ForbiddenHttpException('Not found roles');
        } elseif (!$user->isAdmin() && $user->id != $employeeId) {
            throw new ForbiddenHttpException('AccessDenied ('.$employeeId.')');
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

                        if ($multipleForm->workStart != ""){
                            $uParams->up_work_start_tm = $multipleForm->workStart . ':00';
                            $uParamsNeedSave = true;
                        }

                        if ($multipleForm->timeZone != ""){
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
                                $this->clientChatUserAccessService->disableUserAccessToAllChats($user->id);
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

        if($auth->isSupervision()) {
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

                        // VarDumper::dump($model->form_roles, 10, true); exit;

                        if ($model->form_roles) {
                            $availableRoles = Employee::getAllRoles();
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
								$this->clientChatUserAccessService->setUserAccessToAllChatsByChannelIds($attr['client_chat_user_channel'], $model->id);
							}
						}

                        $transaction->commit();
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


            if(!$user->isSuperAdmin()) {
                if($model->isSuperAdmin()) {
                    throw new NotFoundHttpException('Access denied for Superadmin user: '.$model->id);
                }
            }

            if ($user->isUserManager()) {
                if ($model->isOnlyAdmin()) {
                    throw new NotFoundHttpException('Access denied for Admin user: ' . $model->id);
                }
            }

            if($user->isAnySupervision()) {
                if($model->isAdmin()) {
                    throw new NotFoundHttpException('Access denied for Admin user: '.$model->id);
                }
            }

            $modelUserParams = UserParams::findOne($model->id);
            if(!$modelUserParams) {
                $modelUserParams = new UserParams();
            }



            if($user->isSupervision()) {
                $access = false;

                $userGroups = array_keys($model->getUserGroupList());

                foreach (Yii::$app->user->identity->getUserGroupList() as $grId => $grName) {
                    if(in_array($grId, $userGroups)) {
                        $access = true;
                        break;
                    }
                }

                if(!$access) {
                    throw new ForbiddenHttpException('Access denied for this user (invalid user group)');
                }
            }

            $modelProfile = $model->userProfile;
            //VarDumper::dump($modelProfile->attributes, 10, true); exit;
            if(!$modelProfile) {
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

                $nicknameCCIsChanged = $model->isAttributeChanged('nickname_client_chat');

                if ($model->save()) {

                    if ($model->form_roles) {
                        $availableRoles = Employee::getAllRoles();
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

                        //VarDumper::dump(Yii::$app->request->post(), 10, true); exit;
                        if(!$modelProfile->save()) {
                            //VarDumper::dump($modelProfile->errors,10, true); exit;
                            Yii::error(VarDumper::dumpAsString($modelProfile->errors, 10), 'EmployeeController:actionUpdate:modelProfile:save');
                        }
                    }


                    if(isset($attr['user_groups'])) {
                        UserGroupAssign::deleteAll(['ugs_user_id' => $model->id]);
                        if($attr['user_groups']) {
                            foreach ($attr['user_groups'] as $ugId) {
                                $uga = new UserGroupAssign();
                                $uga->ugs_user_id = $model->id;
                                $uga->ugs_group_id = (int) $ugId;
                                $uga->save();
                            }
                        }
                    }


                    if(isset($attr['user_departments'])) {
                        UserDepartment::deleteAll(['ud_user_id' => $model->id]);
                        if($attr['user_departments']) {
                            foreach ($attr['user_departments'] as $udId) {
                                $ud = new UserDepartment();
                                $ud->ud_user_id = $model->id;
                                $ud->ud_dep_id = (int) $udId;
                                if(!$ud->save()) {
                                    Yii::error(VarDumper::dumpAsString($ud->errors), 'Employee:Create:UserDepartment:save');
                                }
                            }
                        }
                    }

                    if(isset($attr['user_projects'])) {
                        ProjectEmployeeAccess::deleteAll(['employee_id' => $model->id]);
                        if($attr['user_projects']) {
                            foreach ($attr['user_projects'] as $ugId) {
                                $up = new ProjectEmployeeAccess();
                                $up->employee_id = $model->id;
                                $up->project_id = (int) $ugId;
                                $up->created = date('Y-m-d H:i:s');
                                $up->save();
                            }
                        }
                    }

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

                            if (!empty($modelProfile->up_rc_user_id)) {
								$this->clientChatUserAccessService->disableUserAccessToAllChats($model->id);
								$this->clientChatUserAccessService->setUserAccessToAllChatsByChannelIds($attr['client_chat_user_channel'], $model->id);
							} else {
								$this->clientChatUserAccessService->disableUserAccessToAllChats($model->id);
							}
						} else {
							$this->clientChatUserAccessService->disableUserAccessToAllChats($model->id);
						}
                    }

                    //VarDumper::dump($attr['user_groups'], 10, true); exit;


                    /*foreach ($availableProjects as $availableProject) {
                        if (!in_array($availableProject, $newEmployeeAccess) && in_array($availableProject, $model->user_projects)) {
                            ProjectEmployeeAccess::deleteAll([
                                'employee_id' => $model->id,
                                'project_id' => $availableProject
                            ]);
                        } else if (in_array($availableProject, $newEmployeeAccess) && !in_array($availableProject, $model->user_projects)) {
                            $access = new ProjectEmployeeAccess();
                            $access->employee_id = $model->id;
                            $access->project_id = $availableProject;
                            $access->save();
                        }
                    }*/

                    if ($nicknameCCIsChanged && !empty($modelProfile->up_rc_user_id)) {
                        $job = new RocketChatUserUpdateJob();
                        $job->userId = $modelProfile->up_rc_user_id;
                        $job->data = ['name' => $model->nickname_client_chat];

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

            $searchModel = new UserProjectParamsSearch();
            $params = Yii::$app->request->queryParams;

            $params['UserProjectParamsSearch']['upp_user_id'] = $model->id;

            $dataProvider = $searchModel->search($params);

            if ($modelUserParams->load(Yii::$app->request->post())) {

                $modelUserParams->up_user_id = $model->id;
                $modelUserParams->up_updated_user_id = Yii::$app->user->id;

                if($modelUserParams->save()) {
                    return $this->refresh();
                }
            }

        $dataLastFailedLogin = new ActiveDataProvider([
            'query' => UserFailedLogin::find()->andFilterWhere(['ufl_user_id' => $model->id]),
            'sort'=> ['defaultOrder' => ['ufl_id' => SORT_DESC]],
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
                    $text = $item['text'] . ' ('.$item['id'].')';
                    $data[$n]['text'] = self::formatText($text, $q);
                    $data[$n]['selection'] = $item['text'];
                }
            }

            $out['results'] = $data; //array_values($data);
        }
        elseif ($id > 0) {
            $user = Employee::findOne($id);
            $out['results'] = ['id' => $id, 'text' => $user ? $user->username : '', 'selection' => $user ? $user->username : ''];
        }
        return $out;
    }

    /**
     * @param int $id
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionRegisterToRocketChat(int $id): array
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $user = Employee::findOne($id);

        if (Yii::$app->request->isAjax && $user) {
            $out = ['status' => 0, 'message' => ''];

            try {
				$userChannels = $this->clientChatUserChannelRepository->findByUserId($user->id);

				$rocketChat = \Yii::$app->rchat;
                $rocketChat->updateSystemAuth(false);
                $password = $rocketChat::generatePassword();

                $rocketChatUsername = $user->nickname_client_chat ?: $user->username;
                $result = $rocketChat->createUser(
                    $user->username,
                    $password,
					$rocketChatUsername,
                    $user->email
                );

                if (isset($result['error']) && !$result['error']) {

                    if (empty($result['data']['_id'])) {
                        throw new \RuntimeException('Empty result[data][_id]. ' .
                            VarDumper::dumpAsString(['userId' => $id, 'data' => $result]));
                    }
                    if (!$userProfile = UserProfile::findOne(['up_user_id' => $id])) {
                        $userProfile = new UserProfile();
                        $userProfile->up_user_id = $id;
				    }
				    $userProfile->up_rc_user_password = $password;
                    $userProfile->up_rc_user_id = $result['data']['_id'];

                    $login = $rocketChat->login($user->username, $password);

                    if (isset($login['error']) && $login['error']) {
                        throw new \RuntimeException(VarDumper::dumpAsString($login['error']));
                    }

                    if (!empty($login['data']['authToken'])) {
                        $userProfile->up_rc_auth_token = $login['data']['authToken'];
                        $userProfile->up_rc_token_expired = $rocketChat::generateTokenExpired();
                    }

                    if(!$userProfile->save()) {
                        throw new \RuntimeException($userProfile->getErrorSummary(false)[0]);
                    }

                    $userChannels = ArrayHelper::getColumn(ArrayHelper::toArray($userChannels), 'ccuc_channel_id');
                    if ($userChannels) {
						$this->clientChatUserAccessService->setUserAccessToAllChatsByChannelIds($userChannels, $user->id);
					}

				} else {
                    $errorMessage = $rocketChat::getErrorMessageFromResult($result);
                    throw new \RuntimeException('Error from RocketChat. ' . $errorMessage);
                }
                $out['status'] = 1;
                $out['rc_user_id'] = $userProfile->up_rc_user_id;
                $out['rc_user_password'] = $password;
                $out['rc_token_expired'] = $userProfile->up_rc_token_expired;
                $out['rc_auth_token'] = $userProfile->up_rc_auth_token;

            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableFormatter($throwable),
                'EmployeeController:actionRegisterToRocketChat:Throwable');
                $out['message'] = $throwable->getMessage();
            }
            return $out;
        }

        throw new BadRequestHttpException();
    }

	/**
	 * @param int $id
	 * @param ClientChatUserAccessService $clientChatUserAccessService
	 * @return array
	 * @throws BadRequestHttpException
	 */
    public function actionUnRegisterFromRocketChat(int $id): array
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $user = Employee::findOne($id);

        if (Yii::$app->request->isAjax && $user && $userProfile = $user->userProfile) {
            $out = ['status' => 0, 'message' => ''];

            try {
                $rocketChat = \Yii::$app->rchat;
                $rocketChat->updateSystemAuth(false);

                $result = $rocketChat->deleteUser($userProfile->up_rc_user_id, $user->nickname ?: $user->username);

                if (isset($result['error']) && !$result['error']) {

                    $userProfile->up_rc_user_password = null;
                    $userProfile->up_rc_user_id = null;
                    $userProfile->up_rc_auth_token = null;
                    $userProfile->up_rc_token_expired = null;

                    if(!$userProfile->save()) {
                        throw new \RuntimeException($userProfile->getErrorSummary(false)[0]);
                    }

                    $this->clientChatUserAccessService->disableUserAccessToAllChats($userProfile->up_user_id);
                    $this->clientChatMessageService->discardAllUnreadMessagesForUser($userProfile->up_user_id);
                } else {
                    $errorMessage = $rocketChat::getErrorMessageFromResult($result);
                    throw new \RuntimeException('Error from RocketChat. ' . $errorMessage);
                }
                $out['status'] = 1;

            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableFormatter($throwable),
                'EmployeeController:actionUnRegisterFromRocketChat:Throwable');
                $out['message'] = $throwable->getMessage();
            }
            return $out;
        }

        throw new BadRequestHttpException('User or userProfile is required.');
    }

    /**
     * @param string $str
     * @param string $term
     * @return string
     */
    public static function formatText(string $str, string $term): string
    {
        return preg_replace('~'.$term.'~i', '<b style="color: #e15554"><u>$0</u></b>', $str);
    }

    public function actionValidateRocketChatCredential()
    {
        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException();
        }

        try {
            $userId = (int)Yii::$app->request->post('id');
            if (!$userId) {
                throw new \Exception('Not found user Id');
            }
            $user = Employee::findOne($userId);
            if (!$user) {
                throw new \Exception('Not found User with Id ' . $userId);
            }
            if (!$rocketUserId = $user->userProfile->up_rc_user_id) {
                throw new \Exception('Not found Rocket Chat User Id for this user(' . $userId . ')');
            }
            if (!$rocketToken = $user->userProfile->up_rc_auth_token) {
                throw new \Exception('Not found Rocket Chat Auth Token for this user(' . $userId . ')');
            }

            $result = \Yii::$app->rchat->me($rocketUserId, $rocketToken);

            if ($result['error'] !== false) {
                if ($result['error'] === 'You must be logged in to do this.') {
                    throw new \Exception('Invalid credential');
                }
                throw new \Exception((string)$result['error']);
            }

        } catch (\Throwable $e) {
            return $this->asJson([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
        return $this->asJson([
            'error' => false,
            'message' => 'OK',
        ]);
    }
}
