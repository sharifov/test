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
use modules\user\src\update\FieldAccess;
use modules\user\src\update\MultipleUpdateForm;
use modules\user\src\update\UpdateForm;
use modules\user\src\update\UpdateUserException;
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

        $fieldAccess = new FieldAccess(Auth::user(), $model->isNewRecord);
        if (!$fieldAccess->canEdit('acl_rules_activated')) {
            throw new ForbiddenHttpException('Access denied.');
        }

        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException();
        }

        $attr = Yii::$app->request->post($model->formName());
        $model->attributes = $attr;
        if ($model->isNewRecord) {
            $success = $model->save();
            Yii::$app->response->format = Response::FORMAT_JSON;
            $employee = Employee::findOne($model->employee_id);
            return [
                'body' => $this->renderAjax('update/_aclList', [
                    'models' => $employee->employeeAcl,
                    'canEditAclRulesActivated' => $fieldAccess->canEdit('acl_rules_activated'),
                ]),
                'success' => $success
            ];
        }
        return $model->save();
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function oldActionList()
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

        return $this->render('old_list', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'multipleForm' => $multipleForm,
            'multipleErrors' => $multipleErrors,
        ]);
    }

    public function actionList()
    {
        $multipleForm = new MultipleUpdateForm(Auth::user());
        $multipleErrors = [];

        if ($multipleForm->load(Yii::$app->request->post()) && $multipleForm->validate()) {
            if (\is_array($multipleForm->user_list)) {
                foreach ($multipleForm->user_list as $user_id) {
                    $user_id = (int) $user_id;
                    $user = Employee::findOne($user_id);
                    if (!$user) {
                        continue;
                    }

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

                    if ($multipleForm->up_call_expert_limit !== null && $multipleForm->fieldAccess->canEdit('up_call_expert_limit')) {
                        $uParams->up_call_expert_limit = $multipleForm->up_call_expert_limit;
                        $uParamsNeedSave = true;
                    }

                    if ($multipleForm->up_work_start_tm !== null && $multipleForm->fieldAccess->canEdit('up_work_start_tm')) {
                        $uParams->up_work_start_tm = $multipleForm->up_work_start_tm;
                        $uParamsNeedSave = true;
                    }

                    if ($multipleForm->up_timezone !== null && $multipleForm->fieldAccess->canEdit('up_timezone')) {
                        $uParams->up_timezone = $multipleForm->up_timezone;
                        $uParamsNeedSave = true;
                    }

                    if ($multipleForm->up_work_minutes !== null && $multipleForm->fieldAccess->canEdit('up_work_minutes')) {
                        $uParams->up_work_minutes = $multipleForm->up_work_minutes;
                        $uParamsNeedSave = true;
                    }

                    if ($multipleForm->up_inbox_show_limit_leads !== null && $multipleForm->fieldAccess->canEdit('up_inbox_show_limit_leads')) {
                        $uParams->up_inbox_show_limit_leads = $multipleForm->up_inbox_show_limit_leads;
                        $uParamsNeedSave = true;
                    }

                    if ($multipleForm->up_default_take_limit_leads !== null && $multipleForm->fieldAccess->canEdit('up_default_take_limit_leads')) {
                        $uParams->up_default_take_limit_leads = $multipleForm->up_default_take_limit_leads;
                        $uParamsNeedSave = true;
                    }

                    if ($multipleForm->up_min_percent_for_take_leads !== null && $multipleForm->fieldAccess->canEdit('up_min_percent_for_take_leads')) {
                        $uParams->up_min_percent_for_take_leads = $multipleForm->up_min_percent_for_take_leads;
                        $uParamsNeedSave = true;
                    }

                    if ($multipleForm->up_frequency_minutes !== null && $multipleForm->fieldAccess->canEdit('up_frequency_minutes')) {
                        $uParams->up_frequency_minutes = $multipleForm->up_frequency_minutes;
                        $uParamsNeedSave = true;
                    }

                    if ($multipleForm->up_base_amount !== null && $multipleForm->fieldAccess->canEdit('up_base_amount')) {
                        $uParams->up_base_amount = $multipleForm->up_base_amount;
                        $uParamsNeedSave = true;
                    }

                    if ($multipleForm->up_auto_redial !== null && $multipleForm->fieldAccess->canEdit('up_auto_redial')) {
                        $uProfile->up_auto_redial = $multipleForm->up_auto_redial;
                        $uProfileNeedSave = true;
                    }

                    if ($multipleForm->up_kpi_enable !== null && $multipleForm->fieldAccess->canEdit('up_kpi_enable')) {
                        $uProfile->up_kpi_enable = $multipleForm->up_kpi_enable;
                        $uProfileNeedSave = true;
                    }

                    if ($multipleForm->up_leaderboard_enabled !== null && $multipleForm->fieldAccess->canEdit('up_leaderboard_enabled')) {
                        $uParams->up_leaderboard_enabled = $multipleForm->up_leaderboard_enabled;
                        $uParamsNeedSave = true;
                    }

                    if ($multipleForm->up_commission_percent !== null && $multipleForm->fieldAccess->canEdit('up_commission_percent')) {
                        $uParams->up_commission_percent = $multipleForm->up_commission_percent;
                        $uParamsNeedSave = true;
                    }

                    if ($multipleForm->user_departments && $multipleForm->fieldAccess->canEdit('user_departments')) {
                        $transaction = Yii::$app->db->beginTransaction();
                        try {
                            $oldUserDepartmens = $user->getUserDepartmentList();
                            $user->removeAllDepartments();
                            $user->addNewDepartments($multipleForm->user_departments);
                            $transaction->commit();
                            $user->addLog(
                                \Yii::$app->id,
                                Yii::$app->user->id,
                                ["user_departments" => $oldUserDepartmens],
                                ["user_departments" => $multipleForm->getUserDepartmens()]
                            );
                        } catch (\Throwable $e) {
                            $transaction->rollBack();
                            Yii::error($e->getMessage(), 'Employee:list:multipleUpdate:userDepartments');
                            $multipleErrors[$user_id][] = $e->getMessage();
                        }
                    }

                    if ($multipleForm->client_chat_user_channel && $multipleForm->fieldAccess->canEdit('client_chat_user_channel')) {
                        $userClientChatData = UserClientChatData::findOne(['uccd_employee_id' => $user->id]);
                        $transaction = Yii::$app->db->beginTransaction();
                        try {
                            $oldClientChatUserChannel = $user->getClientChatUserChannelList();
                            $user->removeAllClientChatChanels();
                            $user->addClientChatChanels($multipleForm->client_chat_user_channel, Auth::id());
                            if ($userClientChatData && $userClientChatData->isRegisteredInRc()) {
                                $this->clientChatUserAccessService->setUserAccessToAllChatsByChannelIds($multipleForm->client_chat_user_channel, $user->id);
                            } else {
                                $this->clientChatUserAccessService->disableUserAccessToAllChats($user->id);
                            }
                            $transaction->commit();
                            $user->addLog(
                                \Yii::$app->id,
                                Yii::$app->user->id,
                                ["client_chat_user_channel" => $oldClientChatUserChannel],
                                ["client_chat_user_channel" => $multipleForm->getChangedClientChatsChannels()]
                            );
                        } catch (\Throwable $e) {
                            $transaction->rollBack();
                            Yii::error($e->getMessage(), 'Employee:list:multipleUpdate:clientChatChannels');
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

//                    if ($multipleForm->form_roles && $multipleForm->fieldAccess->canEdit('form_roles') && $multipleForm->isChangedRoles()) {
                    if ($multipleForm->form_roles && $multipleForm->fieldAccess->canEdit('form_roles')) {
                        $needToAddRoles = [];
                        $needToRemoveRoles = [];

                        switch ((int)$multipleForm->form_roles_action) {
                            case $multipleForm::ROLE_ADD:
                                foreach ($multipleForm->form_roles as $role) {
                                    if (!in_array($role, $user->getRoles(true))) {
                                        $needToAddRoles[] = $role;
                                    }
                                }

                                break;
                            case $multipleForm::ROLE_REPLACE:
                                $needToRemoveRoles = $user->getRoles(true);
                                $needToAddRoles = $multipleForm->form_roles;
                                break;
                            case $multipleForm::ROLE_REMOVE:
                                foreach ($multipleForm->form_roles as $role) {
                                    if (in_array($role, $user->getRoles(true))) {
                                        $needToRemoveRoles[] = $role;
                                    }
                                }
                                break;
                        }

                        if (!empty($needToAddRoles) || !empty($needToRemoveRoles)) {
                            $transaction = Yii::$app->db->beginTransaction();
                            try {
                                if ($needToRemoveRoles) {
                                    $user->removeRoles($needToRemoveRoles);
                                }

                                if ($needToAddRoles) {
                                    $user->addNewRoles($needToAddRoles);
                                }

                                $transaction->commit();
                            } catch (\Throwable $e) {
                                $transaction->rollBack();
                                Yii::error($e->getMessage(), 'Employee:list:multipleUpdate:userRoles');
                                $multipleErrors[$user_id][] = $e->getMessage();
                            }
                        }
                    }

                    if (empty($multipleForm->form_roles) && $multipleForm->fieldAccess->canEdit('form_roles') && (int)$multipleForm->form_roles_action === $multipleForm::ROLE_REPLACE) {
                        $transaction = Yii::$app->db->beginTransaction();
                        try {
                            $user->removeAllRoles();
                            $transaction->commit();
                        } catch (\Throwable $e) {
                            $transaction->rollBack();
                            Yii::error($e->getMessage(), 'Employee:list:multipleUpdate:userRoles');
                            $multipleErrors[$user_id][] = $e->getMessage();
                        }
                    }

                    if ($multipleForm->fieldAccess->canEdit('user_groups')) {
                        if (!empty($multipleForm->user_groups) || $multipleForm->groupActionIsReplace()) {
                            $oldUserGroupsIds = array_keys($user->getUserGroupList());

                            $groupsForAdd = [];
                            $groupsForDelete = [];

                            switch ($multipleForm->user_groups_action) {
                                case MultipleUpdateForm::GROUP_ADD:
                                    $groupsForAdd = array_diff($multipleForm->user_groups, $oldUserGroupsIds);
                                    break;
                                case MultipleUpdateForm::GROUP_REPLACE:
                                    if (empty($multipleForm->user_groups)) {
                                        $groupsForDelete = $oldUserGroupsIds;
                                    } else {
                                        $groupsForDelete = array_diff($oldUserGroupsIds, $multipleForm->user_groups);
                                    }

                                    break;
                                case MultipleUpdateForm::GROUP_DELETE:
                                    $groupsForDelete = array_intersect($multipleForm->user_groups, $oldUserGroupsIds);
                                    break;
                            }

                            if (!empty($groupsForDelete) || !empty($groupsForAdd)) {
                                $transaction = Yii::$app->db->beginTransaction();

                                try {
                                    if (!empty($groupsForDelete)) {
                                        UserGroupAssign::deleteAll(['and', [ 'ugs_user_id' => $user_id], ['in', 'ugs_group_id', $groupsForDelete]]);
                                    }

                                    if (!empty($groupsForAdd)) {
                                        foreach ($groupsForAdd as $groupId) {
                                            $uga = new UserGroupAssign();
                                            $uga->ugs_user_id = $user->id;
                                            $uga->ugs_group_id = $groupId;

                                            if (!$uga->save()) {
                                                throw new \Exception(VarDumper::dumpAsString($uga->errors));
                                            }
                                        }
                                    }

                                    $transaction->commit();
                                } catch (\Throwable $e) {
                                    $transaction->rollBack();
                                    $multipleErrors[$user_id][] = $e->getMessage();
                                }
                            }
                        }
                    }

                    if (is_numeric($multipleForm->status) && $multipleForm->fieldAccess->canEdit('status')) {
                        $user->status = $multipleForm->status;
                        if (!$user->save(true, ['status'])) {
                            Yii::error(VarDumper::dumpAsString($user->errors), 'Employee:list:multipleUpdate:user:save');
                            $multipleErrors[$user_id][] = $user->getErrors();
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
     *
     * @deprecated
     */
    public function oldActionUpdate()
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

    public function actionUpdate()
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

        if (!$userParams = $targetUser->userParams) {
            $userParams = new UserParams([
                'up_user_id' => $targetUser->id,
                'up_timezone' => 'Europe/Chisinau',
                'up_work_minutes' => 8 * 60,
                'up_work_start_tm' => "16:00",
            ]);
        }
        if (!$userProfile = $targetUser->userProfile) {
            $userProfile = new UserProfile([
                'up_user_id' => $targetUser->id,
                'up_join_date' => date('Y-m-d'),
            ]);
        }

        $form = new UpdateForm($targetUser, $updaterUser, $userParams, $userProfile);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $userUpdated = false;
                $updateRC = [];

                $targetUser->setAttributes($form->getValuesOfAvailableAttributes());
                if (count($targetUser->getDirtyAttributes()) > 0) {
                    if ($targetUser->isAttributeChanged('email')) {
                        $updateRC['email'] = $targetUser->email;
                    }
                    if ($targetUser->isAttributeChanged('nickname')) {
                        $updateRC['name'] = $targetUser->nickname;
                    }
                    if ($form->password) {
                        $targetUser->setPassword($form->password);
                    }
                    if ($targetUser->save()) {
                        $userUpdated = true;
                    } else {
                        throw new UpdateUserException(
                            $targetUser->getErrors(),
                            $targetUser->id,
                            $updaterUser->id,
                            "User(Employee) update error."
                        );
                    }
                }

                if ($form->fieldAccess->canEdit('form_roles') && $form->isChangedRoles()) {
                    try {
                        $targetUser->updateRoles($form->form_roles);
                        $userUpdated = true;
                        $targetUser->addLog(
                            \Yii::$app->id,
                            Yii::$app->user->id,
                            ["roles" => $targetUser->getRoles(true)],
                            ["roles" => $form->form_roles]
                        );
                    } catch (\Throwable $e) {
                        throw new UpdateUserException(
                            ['error' => $e->getMessage()],
                            $targetUser->id,
                            $updaterUser->id,
                            "User(Roles) update error"
                        );
                    }
                }

                $userProfile->setAttributes($form->getValuesOfAvailableAttributes());
                if (count($userProfile->getDirtyAttributes()) > 0) {
                    $userProfile->up_updated_dt = date('Y-m-d H:i:s');
                    if ($userProfile->save()) {
                        $userUpdated = true;
                    } else {
                        throw new UpdateUserException(
                            $userProfile->getErrors(),
                            $targetUser->id,
                            $updaterUser->id,
                            "User(Profile) update error"
                        );
                    }
                }

                $userParams->setAttributes($form->getValuesOfAvailableAttributes());
                if (count($userParams->getDirtyAttributes()) > 0) {
                    $userParams->up_updated_user_id = $updaterUser->id;
                    $userParams->up_updated_dt = date('Y-m-d H:i:s');
                    if ($userParams->save()) {
                        $userUpdated = true;
                    } else {
                        throw new UpdateUserException(
                            $userParams->getErrors(),
                            $targetUser->id,
                            $updaterUser->id,
                            "User(Params) update error"
                        );
                    }
                }

                if ($form->fieldAccess->canEdit('user_groups') && $form->isChangedGroups()) {
                    $oldUserGroups = $targetUser->getUserGroupList();
                    UserGroupAssign::deleteAll(['ugs_user_id' => $targetUser->id]);
                    foreach ($form->user_groups as $groupId) {
                        $uga = new UserGroupAssign();
                        $uga->ugs_user_id = $targetUser->id;
                        $uga->ugs_group_id = $groupId;
                        if (!$uga->save()) {
                            throw new UpdateUserException(
                                $uga->getErrors(),
                                $targetUser->id,
                                $updaterUser->id,
                                "User(Groups) update error"
                            );
                        }
                    }

                    $targetUser->addLog(
                        \Yii::$app->id,
                        Yii::$app->user->id,
                        ["user_groups" => $oldUserGroups],
                        ["user_groups" => $form->getUserGroups()]
                    );

                    $userUpdated = true;
                }

                if ($form->fieldAccess->canEdit('user_departments') && $form->isChangedDepartments()) {
                    $oldUserDepartmens = $targetUser->getUserDepartmentList();
                    UserDepartment::deleteAll(['ud_user_id' => $targetUser->id]);
                    foreach ($form->user_departments as $departmentId) {
                        $ud = new UserDepartment();
                        $ud->ud_user_id = $targetUser->id;
                        $ud->ud_dep_id = $departmentId;
                        if (!$ud->save()) {
                            throw new UpdateUserException(
                                $ud->getErrors(),
                                $targetUser->id,
                                $updaterUser->id,
                                "User(Departments) update error"
                            );
                        }
                    }
                    $targetUser->addLog(
                        \Yii::$app->id,
                        Yii::$app->user->id,
                        ["user_departments" => $oldUserDepartmens],
                        ["user_departments" => $form->getUserDepartmens()]
                    );

                    $userUpdated = true;
                }

                if ($form->fieldAccess->canEdit('user_projects') && $form->isChangedProjects()) {
                    $oldUserProjects = $targetUser->getUserProjectList();
                    ProjectEmployeeAccess::deleteAll(['employee_id' => $targetUser->id]);
                    foreach ($form->user_projects as $projectId) {
                        $up = new ProjectEmployeeAccess();
                        $up->employee_id = $targetUser->id;
                        $up->project_id = $projectId;
                        $up->created = date('Y-m-d H:i:s');
                        if (!$up->save()) {
                            throw new UpdateUserException(
                                $up->getErrors(),
                                $targetUser->id,
                                $updaterUser->id,
                                "User(Project) update error"
                            );
                        }
                    }
                    $targetUser->addLog(
                        \Yii::$app->id,
                        Yii::$app->user->id,
                        ["user_projects" => $oldUserProjects],
                        ["user_projects" => $form->getUserProjects()]
                    );

                    $userUpdated = true;
                }

                $userClientChatData = UserClientChatData::findOne(['uccd_employee_id' => $targetUser->id]);

                if ($form->fieldAccess->canEdit('client_chat_user_channel') && $form->isChangedClientChatsChannels()) {
                    $oldClientChatUserChannel = $targetUser->getClientChatUserChannelList();
                    ClientChatUserChannel::deleteAll(['ccuc_user_id' => $targetUser->id]);
                    if ($form->client_chat_user_channel) {
                        foreach ($form->client_chat_user_channel as $channelId) {
                            $clientChatChanel = new ClientChatUserChannel();
                            $clientChatChanel->ccuc_user_id = $targetUser->id;
                            $clientChatChanel->ccuc_channel_id = $channelId;
                            $clientChatChanel->ccuc_created_dt = date('Y-m-d H:i:s');
                            $clientChatChanel->ccuc_created_user_id = $updaterUser->id;
                            if (!$clientChatChanel->save()) {
                                throw new UpdateUserException(
                                    $clientChatChanel->getErrors(),
                                    $targetUser->id,
                                    $updaterUser->id,
                                    "User(ClientChatUserChannel) update error"
                                );
                            }
                        }

                        $targetUser->addLog(
                            \Yii::$app->id,
                            Yii::$app->user->id,
                            ["client_chat_user_channel" => $oldClientChatUserChannel],
                            ["client_chat_user_channel" => $form->getChangedClientChatsChannels()]
                        );

                        if ($userClientChatData && $userClientChatData->isRegisteredInRc()) {
                            $this->clientChatUserAccessService->setUserAccessToAllChatsByChannelIds($form->client_chat_user_channel, $targetUser->id);
                        } else {
                            $this->clientChatUserAccessService->disableUserAccessToAllChats($targetUser->id);
                        }
                    } else {
                        $this->clientChatUserAccessService->disableUserAccessToAllChats($targetUser->id);
                    }
                    TagDependency::invalidate(Yii::$app->cache, ClientChatUserChannel::cacheTags($targetUser->id));

                    $userUpdated = true;
                }

                if ($form->fieldAccess->canEdit('user_shift_assigns') && $form->isChangedUserShiftAssign()) {
                    $oldUserShiftAssign = $targetUser->getUserShiftAssignList();
                    UserShiftAssign::deleteAll(['usa_user_id' => $targetUser->id]);
                    foreach ($form->user_shift_assigns as $shiftId) {
                        try {
                            $userShiftAssign = UserShiftAssign::create($targetUser->id, $shiftId);
                            (new UserShiftAssignRepository($userShiftAssign))->save(true);
                        } catch (\Throwable $throwable) {
                            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), [
                                'userId' => $targetUser->id,
                                'shiftId' => $shiftId,
                            ]);
                            throw new UpdateUserException(
                                ['error' => $message],
                                $targetUser->id,
                                $updaterUser->id,
                                "User(Shift) update error"
                            );
                        }
                    }

                    $targetUser->addLog(
                        \Yii::$app->id,
                        Yii::$app->user->id,
                        ["user_shift_assigns" => $oldUserShiftAssign],
                        ["user_shift_assigns" => $form->getChangedUserShiftAssign()]
                    );

                    $userUpdated = true;
                }

                if (!empty($updateRC) && $userClientChatData && $userClientChatData->isRegisteredInRc()) {
                    $job = new RocketChatUserUpdateJob();
                    $job->userId = $userClientChatData->getRcUserId();
                    $job->data = $updateRC;
                    $job->userClientChatDataId = $userClientChatData->uccd_id;
                    Yii::$app->queue_job->priority(10)->push($job);
                }

                $transaction->commit();

                if ($userUpdated) {
                    Yii::$app->getSession()->setFlash('success', 'User updated');
                } else {
                    Yii::$app->getSession()->setFlash('warning', 'User is not updated');
                }
            } catch (UpdateUserException $e) {
                $transaction->rollBack();
                Yii::error([
                    'error' => $e->getMessage(),
                    'errors' => $e->errors,
                    'targetUserId' => $targetUser->id,
                    'updaterUserId' => $updaterUser->id
                ], 'EmployeeController:update');
                Yii::$app->getSession()->setFlash('warning', 'User is not updated: ' . $e->getMessage());
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::error([
                    'error' => $e->getMessage(),
                    'targetUserId' => $targetUser->id,
                    'updaterUserId' => $updaterUser->id
                ], 'EmployeeController:update');
                Yii::$app->getSession()->setFlash('warning', 'User is not updated. Server error.');
            }
        }

        $userProjectParamsSearch = new UserProjectParamsSearch();
        $params = Yii::$app->request->queryParams;
        $params['UserProjectParamsSearch']['upp_user_id'] = $targetUser->id;
        $userProjectParamsDataProvider = $userProjectParamsSearch->search($params);

        $lastFailedLoginDataProvider = new ActiveDataProvider([
            'query' => UserFailedLogin::find()->andFilterWhere(['ufl_user_id' => $targetUser->id]),
            'sort' => ['defaultOrder' => ['ufl_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $result = [
            'form' => $form,
            'userProjectParamsDataProvider' => $userProjectParamsDataProvider,
            'lastFailedLoginDataProvider' => $lastFailedLoginDataProvider,
            'userProductTypeDataProvider' => null,
        ];

        $userVoiceMailSearch = new UserVoiceMailSearch();
        $result['userVoiceMailDataProvider'] = $userVoiceMailSearch->search(['UserVoiceMailSearch' => ['uvm_user_id' => $targetUser->id]]);

        if (Auth::can('user-product-type/list')) {
            $userProductTypeDataProvider = new ActiveDataProvider([
                'query' => UserProductType::find()->andFilterWhere(['upt_user_id' => $targetUser->id])
            ]);
            $result['userProductTypeDataProvider'] = $userProductTypeDataProvider;
        }
        return $this->render('update/_form', $result);
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionEmployeeValidationUpdate(): array
    {
        $targetUserId = (int)Yii::$app->request->get('id');
        if (!$targetUserId) {
            throw new BadRequestHttpException('Invalid request');
        }
        $targetUser = Employee::findOne($targetUserId);

        $updaterUser = Auth::user();

        if (!$userParams = $targetUser->userParams) {
            $userParams = new UserParams([
                'up_user_id' => $targetUser->id,
                'up_timezone' => 'Europe/Chisinau',
                'up_work_minutes' => 8 * 60,
                'up_work_start_tm' => "16:00",
            ]);
        }
        if (!$userProfile = $targetUser->userProfile) {
            $userProfile = new UserProfile([
                'up_user_id' => $targetUser->id,
                'up_join_date' => date('Y-m-d'),
            ]);
        }

        $form = new UpdateForm($targetUser, $updaterUser, $userParams, $userProfile);

        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }
        throw new BadRequestHttpException();
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
