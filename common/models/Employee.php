<?php

namespace common\models;

use borales\extensions\phoneInput\PhoneInput;
use borales\extensions\phoneInput\PhoneInputValidator;
use common\components\BackOffice;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\IdentityInterface;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "employees".
 *
 * @property int $id
 * @property string $username
 * @property string $full_name
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $last_activity
 * @property boolean $acl_rules_activated
 *
 * @property array $roles
 * @property array $roles_raw
 * @property array $rolesName
 * @property array $form_roles
 *
 * @property Lead[] $leads
 * @property EmployeeAcl[] $employeeAcl
 * @property ProjectEmployeeAccess[] $projectEmployeeAccesses
 * @property Project[] $projects
 *
 * @property UserGroupAssign[] $userGroupAssigns
 * @property UserGroup[] $ugsGroups
 * @property UserParams $userParams
 *
 * @property UserProjectParams[] $userProjectParams
 * @property Project[] $uppProjects
 * @property UserProfile $userProfile
 */
class Employee extends \yii\db\ActiveRecord implements IdentityInterface
{
    public const SCENARIO_REGISTER = 'register';

    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE = 10;

    public const PROFIT_BONUSES = [
        11000 => 500,
        8000 => 300,
        5000 => 150
    ];

    public $password;
    public $deleted;

    public $roles = null;
    public $roles_raw = null;
    public $rolesName = null;
    public $form_roles = [];

    public $viewItemsEmployeeAccess;

    public $user_groups;
    public $user_projects;

    public $shiftData = [];
    public $currentShiftTaskInfoSummary = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employees';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'auth_key', 'password_hash', 'email', 'form_roles'], 'required'],
            [['password'], 'required', 'on' => self::SCENARIO_REGISTER],
            [['email', 'password', 'username'], 'trim'],
            [['password'], 'string', 'min' => 6],
            [['status'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            ['email', 'email'],
            [['password_reset_token'], 'unique'],
            [['created_at', 'updated_at', 'last_activity', 'acl_rules_activated', 'full_name', 'user_groups', 'user_projects', 'deleted'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'user_groups' => 'User groups',
            'user_projects' => 'Projects access',
            'form_roles' => 'Roles'
        ];
    }


    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function afterFind()
    {
        parent::afterFind();

        //var_dump(\webapi\models\ApiUser::class); die;

        /*if (isset(Yii::$app->user)) {
            if (Yii::$app->user && Yii::$app->user->identityClass === \webapi\models\ApiUser::class) {
                $this->roles = [];
            } else {
                $roles = $this->getRoles();
                $this->roles = array_keys($roles);
            }
        }*/
    }


    /**
     * @return bool
     */
    public function isDeleted() : bool
    {
        return !(boolean)$this->status;
    }


    /**
     * @param string $role
     * @return bool
     */
    public function canRole(string $role = '') : bool
    {
        if($this->roles === null) {
            $roles = $this->getRoles();
            $this->roles = array_keys($roles);
        }

        return in_array($role, $this->roles, false);
    }

    /**
     * @param array $roles
     * @return bool
     */
    public function canRoles(array $roles = []) : bool
    {
        foreach ($roles as $role) {
            if ($this->canRole($role)) {
                return true;
            }
        }
        return false;
    }

    /*public function afterValidate()
    {
        parent::afterValidate();

        $this->updated_at = date('Y-m-d H:i:s');
    }*/

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::class, ['up_user_id' => 'id']);
    }

    public function canCall()
    {
        return ($this->userProfile && $this->userProfile->canCall());
    }

    public function isKpiEnable()
    {
        return ($this->userProfile && $this->userProfile->isKpiEnable());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeeAcl()
    {
        return $this->hasMany(EmployeeAcl::class, ['employee_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectEmployeeAccesses()
    {
        return $this->hasMany(ProjectEmployeeAccess::class, ['employee_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProjectParams()
    {
        return $this->hasMany(UserProjectParams::class, ['upp_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUppProjects()
    {
        return $this->hasMany(Project::class, ['id' => 'upp_project_id'])->viaTable('user_project_params', ['upp_user_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjects()
    {
        return $this->hasMany(Project::class, ['id' => 'project_id'])->viaTable('project_employee_access', ['employee_id' => 'id']);
    }


    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserParams()
    {
        return $this->hasOne(UserParams::className(), ['up_user_id' => 'id']);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    public static function getAllEmployees()
    {
        return ArrayHelper::map(self::find()->where(['status' => self::STATUS_ACTIVE])->all(), 'id', 'username');
    }

    /**
     * @return array
     */
    public static function getAllRoles(): array
    {

        $auth = \Yii::$app->authManager;

        /*$query = new Query();
        $result = $query->select(['name', 'description'])
            ->from('auth_item')->where(['type' => 1])
            ->all();*/

        $result = $auth->getRoles();
        $roles = ArrayHelper::map($result, 'name', 'description');

        if(!Yii::$app->user->identity->canRole('admin') && !Yii::$app->user->identity->canRole('superadmin')) {
            if(isset($roles['admin'])) {
                unset($roles['admin']);
            }

            if(isset($roles['supervision'])) {
                unset($roles['supervision']);
            }
        }

        if(!Yii::$app->user->identity->canRole('admin') && !Yii::$app->user->identity->canRole('superadmin') && !Yii::$app->user->identity->canRole('userManager') ) {
            if(isset($roles['qa'])) {
                unset($roles['qa']);
            }
        }

        if(!Yii::$app->user->identity->canRole('superadmin')) {
            if(isset($roles['superadmin'])) {
                unset($roles['superadmin']);
            }
        }

        //VarDumper::dump($roles, 10, true);

        return $roles;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeads()
    {
        return $this->hasMany(Lead::class, ['employee_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function prepareSave($attr)
    {
        $this->username = $attr['username'];
        $this->email = $attr['email'];
        $this->full_name = $attr['full_name'];
        $this->password = $attr['password'];
        if (!empty($this->password)) {
            $this->setPassword($this->password);
        }
        if (isset($attr['deleted'])) {
            $this->status = empty($attr['deleted'])
                ? self::STATUS_ACTIVE : self::STATUS_DELETED;
        }
        if (isset($attr['acl_rules_activated'])) {
            $this->acl_rules_activated = $attr['acl_rules_activated'];
        }
        if (isset($attr['form_roles']) && $attr['form_roles']) {
            foreach ($attr['form_roles'] as $role) {
                $this->form_roles[] = $role;
            }
        }
        $this->generateAuthKey();

        return $this->isNewRecord;
    }


    /**
     * @param bool $isNew
     * @throws \Exception
     */
    public function addRole(bool $isNew = false) : void
    {
        $auth = \Yii::$app->authManager;

        if (!$isNew) {
            $auth->revokeAll($this->id);
        }

        if ($this->form_roles) {
            foreach ($this->form_roles as $role) {
                $authorRole = $auth->getRole($role);
                $auth->assign($authorRole, $this->id);
            }
        }
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * @return array
     */
    public function getRoles() : array
    {
        if ($this->rolesName === null) {
            $this->rolesName = ArrayHelper::map(Yii::$app->authManager->getRolesByUser($this->id), 'name', 'description');
        }
        return $this->rolesName;
    }

    public function getRolesRaw()
    {
        if(!$this->id) return [];
        if(NULL !== $this->roles_raw) {
            return $this->roles_raw;
        }
        $items = [];
        $connection = \Yii::$app->getDb();
        $command = $connection->createCommand("SELECT item_name FROM auth_assignment WHERE user_id = " . $this->id);
        $items = $command->queryAll();
        if(count($items)) {
            return ArrayHelper::map($items, 'item_name', 'item_name');
        }
        return $items;
    }

    /**
     * @return array
     */
    public static function getList(): array
    {
        $data = self::find()->orderBy(['username' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data, 'id', 'username');
    }

    /**
     * @return array
     */
    public static function getListByRole($role = 'agent'): array
    {
        $data = self::find()->leftJoin('auth_assignment','auth_assignment.user_id = id')->andWhere(['auth_assignment.item_name' => $role])->orderBy(['username' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data, 'id', 'username');
    }


    /**
     * @param int|null $user_id
     * @return array
     */
    public static function getListByUserId(int $user_id = null): array
    {
        $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $user_id]);
        $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);

        $query = self::find()->orderBy(['username' => SORT_ASC])->asArray();
        $query->andWhere(['IN', 'employees.id', $subQuery]);

        $data = $query->all();
        return ArrayHelper::map($data, 'id', 'username');
    }


    /**
     * @return array
     */
    public static function getListByProject($projectId, $withExperts = false): array
    {
        if (Yii::$app->user->identity->canRoles(['admin', 'supervision'])) {
            $data = self::find()
                ->orderBy(['username' => SORT_ASC])
                ->asArray()->all();
        } else {
            $data = self::find()
                ->joinWith('projectEmployeeAccesses')
                ->where(['=', 'project_id', $projectId])
                ->orderBy(['username' => SORT_ASC])
                ->asArray()->all();
        }

        if ($withExperts) {
            $experts = Yii::$app->cache->get(sprintf('list-of-experts-from-BO'));
            if ($experts === false) {
                $result = BackOffice::sendRequest('default/experts');
                if (!empty($result)) {
                    $experts = $result;
                    Yii::$app->cache->set(sprintf('list-of-experts-from-BO'), $experts, 21600);
                }
            }
            if (is_array($experts)) {
                $employeesGroup = [
                    'Sales' => ArrayHelper::map($data, 'id', 'username'),
                    'Experts' => $experts
                ];
                $options = [];
                foreach ($employeesGroup as $type => $group) {
                    $child_options = [];
                    foreach ($group as $id => $employee) {
                        $employeeId = ($type != 'Experts')
                            ? $id
                            : $employee;

                        //if(is_int($employeeId)){
                        $child_options[$employeeId] = $employee;
                        //}

                    }
                    $options[$type] = $child_options;
                }
                return $options;
            }
        }

        return ArrayHelper::map($data, 'id', 'username');
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserGroupAssigns()
    {
        return $this->hasMany(UserGroupAssign::class, ['ugs_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUgsGroups()
    {
        return $this->hasMany(UserGroup::class, ['ug_id' => 'ugs_group_id'])->viaTable('user_group_assign', ['ugs_user_id' => 'id']);
    }

    /**
     * @return array
     */
    public function getUserGroupList(): array
    {
        $groups = [];
        if ($groupsModel = $this->ugsGroups) {
            $groups = \yii\helpers\ArrayHelper::map($groupsModel, 'ug_id', 'ug_name');
        }

        return $groups;
    }

    /**
     * Deletes an existing Employee model.
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
     * Finds the Employee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Employee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Employee::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /**
     * @return array|mixed
     * @throws \Exception
     */
    public function getCurrentShiftTaskInfoSummary()
    {

        if ($this->currentShiftTaskInfoSummary) {
            return $this->currentShiftTaskInfoSummary;
        }

        $shiftTime = $this->getShiftTime();

        $stats = [];
        if (isset($shiftTime['start_utc_dt']) && $shiftTime['start_utc_dt']) {
            $startShiftDate = date('Y-m-d', strtotime($shiftTime['start_utc_dt']));
        } else {
            $startShiftDate = date('Y-m-d');
        }

        //VarDumper::dump($startShiftDate, 10, true); exit;

        $taskListAllQuery = \common\models\LeadTask::find()
            ->where(['lt_user_id' => $this->id])
            ->andWhere(['=', 'lt_date', $startShiftDate]);

        $taskListCheckedQuery = \common\models\LeadTask::find()
            ->where(['lt_user_id' => $this->id])
            ->andWhere(['IS NOT', 'lt_completed_dt', null])
            ->andWhere(['=', 'lt_date', $startShiftDate]);


        $taskListAllQuery->joinWith(['ltLead' => function ($q) {
            $q->where(['NOT IN', 'leads.status', [Lead::STATUS_TRASH, Lead::STATUS_SNOOZE]]);
        }]);

        $taskListCheckedQuery->joinWith(['ltLead' => function ($q) {
            $q->where(['NOT IN', 'leads.status', [Lead::STATUS_TRASH, Lead::STATUS_SNOOZE]]);
        }]);


        $completedTasksCount = (int)$taskListCheckedQuery->count();
        $allTasksCount = (int)$taskListAllQuery->count();


        $stats['completedTasksCount'] = $completedTasksCount;
        $stats['allTasksCount'] = $allTasksCount;


        if ($allTasksCount > 0) {
            $completedTasksPercent = round($completedTasksCount * 100 / $allTasksCount);
        } else {
            $completedTasksPercent = 0;
        }

        $stats['completedTasksPercent'] = $completedTasksPercent;
        $this->currentShiftTaskInfoSummary = $stats;
        return $stats;
    }

    /**
     * @return string
     */
    public function getLastTakenLeadDt():string
    {
        $leadFlow = LeadFlow::find()
                        ->where(['employee_id' => $this->id])
                        ->andWhere(['status' => 2, 'lf_from_status_id' => 1])
                        ->orderBy(['created' => SORT_DESC])
                        ->one();

       return ($leadFlow)?$leadFlow->created:'';
    }


    /**
     * @param string|null $start_dt
     * @param string|null $end_dt
     * @return string
     */
    public function getTaskStats(string $start_dt = null, string $end_dt = null): string
    {

        if ($start_dt) {
            $start_dt = date('Y-m-d', strtotime($start_dt));
        } else {
            $start_dt = null;
        }

        if ($end_dt) {
            $end_dt = date('Y-m-d', strtotime($end_dt));
        } else {
            $end_dt = null;
        }


        $taskListAllQuery = \common\models\LeadTask::find()->select(['COUNT(*) AS field_cnt', 'lt_task_id'])
            ->where(['lt_user_id' => $this->id])
            ->andFilterWhere(['>=', 'lt_date', $start_dt])
            ->andFilterWhere(['<=', 'lt_date', $end_dt])
            ->groupBy(['lt_task_id'])
            ->orderBy(['lt_task_id' => SORT_ASC]);

        $taskListCheckedQuery = \common\models\LeadTask::find()->select(['COUNT(*) AS field_cnt', 'lt_task_id'])
            ->where(['lt_user_id' => $this->id])
            ->andWhere(['IS NOT', 'lt_completed_dt', null])
            ->andFilterWhere(['>=', 'lt_date', $start_dt])
            ->andFilterWhere(['<=', 'lt_date', $end_dt])
            ->groupBy(['lt_task_id']);


        $taskListAllQuery->joinWith(['ltLead' => function ($q) {
            $q->where(['NOT IN', 'leads.status', [Lead::STATUS_TRASH, Lead::STATUS_SNOOZE]]);
        }]);

        $taskListCheckedQuery->joinWith(['ltLead' => function ($q) {
            $q->where(['NOT IN', 'leads.status', [Lead::STATUS_TRASH, Lead::STATUS_SNOOZE]]);
        }]);


        $taskListAll = $taskListAllQuery->all();
        $taskListChecked = $taskListCheckedQuery->all();


        //return $taskListChecked->createCommand()->getRawSql();

        $completed = [];
        if ($taskListChecked) {
            foreach ($taskListChecked as $taskItem) {
                $completed[$taskItem->lt_task_id] = $taskItem->field_cnt;
            }
        }

        //$itemHeader = [];
        $item = [];


        if ($taskListAll) {
            foreach ($taskListAll as $task) {
                //$itemHeader[] = Html::encode($task->ltTask->t_name);
                //$item[] = ''.($completed[$task->lt_task_id] ?? 0).'/'. $task->field_cnt.'';

                $completedTasks = $completed[$task->lt_task_id] ?? 0;

                $str = '<tr><td><small>' . Html::encode($task->ltTask->t_name) . '</small></td><td><small>' . $completedTasks . ' / ' . Html::a($task->field_cnt,
                        ['lead-task/index', 'LeadTaskSearch[lt_task_id]' => $task->lt_task_id, 'LeadTaskSearch[lt_user_id]' => $this->id],
                        ['data-pjax' => 0, 'target' => '_blank']) . '</small></td>';


                if ($task->field_cnt > 0) {
                    $percent = (int)($completedTasks * 100 / $task->field_cnt);
                } else {
                    $percent = 0;
                }

                $str .= '<td width="100"><div class="progress" style="margin-bottom: 0" title="' . $percent . '%">
                          <div class="progress-bar" role="progressbar" aria-valuenow="' . $percent . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $percent . '%;">
                            ' . $percent . '%
                          </div>
                        </div></td></tr>';


                $item[] = $str;
            }
        }


        if ($item) {
            $str = '<table class="table table-bordered table-condensed">';
            //$str .= '<tr><th class="text-center">'.implode('</th><th class="text-center">', $itemHeader).'</th></tr>';

            //$str .= '<tr>';
            //$str .= '<td class="text-center">' . implode('</td><td class="text-center">', $item) . '</td>';
            //$str .= '</tr>';
            $str .= implode('', $item);
            $str .= '</table>';
        } else {
            $str = '-';
        }

        return $str;
    }

    /**
     * @return array
     */
    public function paramsForSalary() : array
    {
        $data = [];
        if($this->userParams) {
            $data['base_amount'] = is_numeric($this->userParams->up_base_amount) ? (float) $this->userParams->up_base_amount : 0;
            $data['commission_percent'] = is_numeric($this->userParams->up_commission_percent) ? (float) $this->userParams->up_commission_percent : 0;
            $data['bonus_active'] = is_numeric($this->userParams->up_bonus_active) ? $this->userParams->up_bonus_active : 0;
        } else {
            $data['base_amount'] = 200;
            $data['commission_percent'] = 10;
            $data['bonus_active'] = 1;
        }

        $data['profit_bonuses'] = $this->getProfitBonuses();
        if(empty($data['profit_bonuses'])){
            $data['profit_bonuses'] = self::PROFIT_BONUSES;
        }

        return $data;
    }

    /*
     * @param startDate DateTime
     * @param endDat DateTime
     *
     * */
    public function calculateSalaryBetween($startDate, $endDate)
    {
        $paramsForSalary = $this->paramsForSalary();
        $base = $paramsForSalary['base_amount'];
        $commission = $paramsForSalary['commission_percent'];
        $bonusActive = $paramsForSalary['bonus_active'];
        $bonus = 0;

        $query = new Query();
        $query->select([
            'lead_id' => 'l.id',
            'final_profit' => 'l.final_profit',
            'agents_processing_fee' => 'l.agents_processing_fee',
            'q_id' => 'q.id',
            'pax_cnt' => '(l.adults + l.children)',
            'fare_type' => 'q.fare_type',
            'check_payment' => 'q.check_payment',
            'tips' => 'l.tips',
            'agent_type' => "(CASE WHEN l.employee_id = $this->id THEN 'main' ELSE 'split' END)",
            'minus_percent_profit' => 'SUM(ps.ps_percent)',
            'split_percent_profit' => "SUM(CASE WHEN ps.ps_user_id = $this->id THEN ps.ps_percent ELSE 0 END)",
            'minus_percent_tips' => 'SUM(ts.ts_percent)',
            'split_percent_tips' => "SUM(CASE WHEN ts.ts_user_id = $this->id THEN ts.ts_percent ELSE 0 END)"
        ])
        ->from(Lead::tableName() . ' l')
        ->leftJoin(Quote::tableName() . ' q', 'q.lead_id = l.id')
        ->leftJoin(ProfitSplit::tableName() . ' ps', 'ps.ps_lead_id = l.id')
        ->leftJoin(TipsSplit::tableName() . ' ts', 'ts.ts_lead_id = l.id')
        ->where(['l.status' => Lead::STATUS_SOLD, 'q.status' => Quote::STATUS_APPLIED])
        ->andWhere('l.employee_id = ' . $this->id . ' OR ps.ps_user_id = ' . $this->id. ' OR ts.ts_user_id = ' . $this->id)
        ->groupBy(['q.id', 'l.id']);

        if ($startDate !== null || $endDate !== null) {
            $subQuery = LeadFlow::find()->select(['DISTINCT(lead_flow.lead_id)'])->where('lead_flow.status = l.status AND lead_flow.lead_id = l.id');
            if ($startDate !== null) {
                $subQuery->andFilterWhere(['>=', 'DATE(lead_flow.created)', $startDate->format('Y-m-d')]);
            }
            if ($endDate !== null) {
                $subQuery->andFilterWhere(['<=', 'DATE(lead_flow.created)', $endDate->format('Y-m-d')]);
            }
            $query->andWhere(['IN', 'l.id', $subQuery]);
        }

        //echo $query->createCommand()->getRawSql();die;
        $res = $query->all();

        $profit = 0;
        foreach ($res as $entry) {
            $entry['minus_percent_profit'] = intval($entry['minus_percent_profit']);
            $entry['minus_percent_tips'] = intval($entry['minus_percent_tips']);
            $quote = Quote::findOne(['id' => $entry['q_id']]);
            if($entry['final_profit'] !== null){
                $totalProfit = $entry['final_profit'];
                $agentsProcessingFee = ($entry['agents_processing_fee'])?$entry['agents_processing_fee']:$entry['pax_cnt']*Lead::AGENT_PROCESSING_FEE_PER_PAX;
                $totalProfit -= $agentsProcessingFee;
            }else{
                $totalProfit = $quote->getEstimationProfit();
            }
            $totalTips = $entry['tips']/2;
            if ($entry['agent_type'] == 'main') {
                $agentProfit = $totalProfit * (100 - $entry['minus_percent_profit']) / 100;
                $agentTips = ($totalTips > 0)?($totalTips * (100 - $entry['minus_percent_tips']) / 100):0;
            } else {
                $agentProfit = $totalProfit * $entry['split_percent_profit'] / 100;
                $agentTips = ($totalTips > 0)?($totalTips * $entry['split_percent_tips'] / 100):0;
            }
            $profit += $agentProfit + $agentTips;
        }

        if ($bonusActive) {
            $profitBonuses = $paramsForSalary['profit_bonuses'];
            foreach ($profitBonuses as $profKey => $bonusVal) {
                if ($profit >= $profKey) {
                    $bonus = $bonusVal;
                    break;
                }
            }
        }

        $startProfit = $profit;
        $profit = $profit * $commission / 100;

        return [
            'salary' => $profit + $base + $bonus,
            'base' => $base,
            'bonus' => $bonus,
            'startProfit' => $startProfit,
            'commission' => $commission,
        ];
    }


    /**
     * @param array $statusList
     * @param string|null $startDate
     * @param string|null $endDate
     * @return int
     */
    public function getLeadCountByStatus(array $statusList = [], string $startDate = null, string $endDate = null): int
    {
        if ($startDate) {
            $startDate = date('Y-m-d', strtotime($startDate));
        }

        if ($endDate) {
            $endDate = date('Y-m-d', strtotime($endDate));
        }

        $query = LeadFlow::find()->select('COUNT(DISTINCT(lead_id))')->where(['employee_id' => $this->id, 'status' => $statusList]);
        $query->andFilterWhere(['>=', 'created', $startDate]);
        $query->andFilterWhere(['<=', 'created', $endDate]);
        $count = $query->asArray()->scalar();
        return $count;
    }


    /**
     * @param array $statusList
     * @param int|null $from_status_id
     * @param string|null $startDate
     * @param string|null $endDate
     * @return int
     */
    public function getLeadCountByStatuses(array $statusList = [], int $from_status_id = null, string $startDate = null, string $endDate = null): int
    {
        if ($startDate) {
            $startDate = date('Y-m-d', strtotime($startDate));
        }

        if ($endDate) {
            $endDate = date('Y-m-d', strtotime($endDate));
        }

        $query = LeadFlow::find()->select('COUNT(DISTINCT(lead_id))')->where(['employee_id' => $this->id, 'status' => $statusList]);

        if($from_status_id > 0) {
            $query->andWhere(['lf_from_status_id' => $from_status_id]);
        }

        $query->andFilterWhere(['>=', 'created', $startDate]);
        $query->andFilterWhere(['<=', 'created', $endDate]);
        $count = $query->asArray()->scalar();
        return $count;
    }

    public function getProfitBonuses()
    {
        $pb = ProfitBonus::find()->where(['pb_user_id' => $this->id])->orderBy(['pb_min_profit' => SORT_DESC])->all();
        $data = [];
        foreach ($pb as $entry) {
            $data[$entry['pb_min_profit']] = $entry['pb_bonus'];
        }
        return $data;
    }

    /**
     * @return array
     */
    public static function timezoneList(): array
    {
        $timezoneIdentifiers = \DateTimeZone::listIdentifiers(\DateTimeZone:: ALL);
        $utcTime = new \DateTime('now', new \DateTimeZone('UTC'));

        $tempTimezones = array();
        foreach ($timezoneIdentifiers as $timezoneIdentifier) {
            $currentTimezone = new \DateTimeZone($timezoneIdentifier);

            $tempTimezones[] = array(
                'offset' => (int)$currentTimezone->getOffset($utcTime),
                'identifier' => $timezoneIdentifier
            );
        }

        // Sort the array by offset,identifier ascending
        usort($tempTimezones, function ($a, $b) {
            return ($a['offset'] === $b['offset'])
                ? strcmp($a['identifier'], $b['identifier'])
                : $a['offset'] - $b['offset'];
        });

        $timezoneList = array();
        foreach ($tempTimezones as $tz) {
            $sign = ($tz['offset'] > 0) ? '+' : '-';
            $offset = gmdate('H:i', abs($tz['offset']));
            $timezoneList[$tz['identifier']] = '(UTC ' . $sign . $offset . ') ' .
                $tz['identifier'];
        }

        return $timezoneList;
    }


    /**
     * @return array
     * @throws \Exception
     */
    public function getShiftTime(): array
    {
        $shiftData = [];

        if ($this->shiftData) {
            return $this->shiftData;
        }

        if ($this->userParams) {
            $this->userParams->up_work_minutes = ($this->userParams->up_work_minutes)?:480;
            $startTime = $this->userParams->up_work_start_tm;
            $workHours = (int)$this->userParams->up_work_minutes * 60;
            $timeZone = $this->userParams->up_timezone ?: 'UTC';

            if ($startTime && $workHours) {
                $currentTimeUTC = new \DateTime();
                $currentTimeUTC->setTimezone(new \DateTimeZone('UTC'));

                $startShiftTimeUTC = new \DateTime(date('Y-m-d') . ' ' . $startTime, new \DateTimeZone($timeZone));
                $startShiftTimeUTC->setTimezone(new \DateTimeZone('UTC'));

                $endShiftTimeUTC = clone $startShiftTimeUTC;
                $endShiftTimeUTC->add(new \DateInterval('PT' . $workHours . 'S'));

                $endShiftMinutes = $endShiftTimeUTC->format('H')*60 + $endShiftTimeUTC->format('i');
                $currentMinutes = $currentTimeUTC->format('H')*60 + $currentTimeUTC->format('i');

                if($startShiftTimeUTC->format('d') != $endShiftTimeUTC->format('d')){
                    //var_dump($currentMinutes, $endShiftMinutes, ($currentMinutes >= 0 && $endShiftMinutes >= $currentMinutes));
                    if($currentMinutes >= 0 && $endShiftMinutes >= $currentMinutes){
                        $startShiftTimeUTC->modify('-1 day');
                        $endShiftTimeUTC->modify('-1 day');
                    }

                }
                // $startShiftTimeDt = $startShiftTimeUTC->format('Y-m-d H:i:s');
                // $endShiftTimeDt = $endShiftTimeUTC->format('Y-m-d H:i:s');
                // echo $startShiftTimeUTC.' - '.$endShiftTimeUTC; exit;

                $startTS = $startShiftTimeUTC->getTimestamp();
                $endTS = $endShiftTimeUTC->getTimestamp();

                $shiftData['start_utc_ts'] = $startTS;
                $shiftData['end_utc_ts'] = $endTS;

                $shiftData['start_period_utc_ts'] = $endTS - (24 * 60 * 60);

                $shiftData['start_utc_dt'] = $startShiftTimeUTC->format('Y-m-d H:i:s');
                $shiftData['end_utc_dt'] = $endShiftTimeUTC->format('Y-m-d H:i:s');

                $shiftData['start_period_utc_dt'] = date('Y-m-d H:i:s', $shiftData['start_period_utc_ts']);
            }
            $this->shiftData = $shiftData;
        }

        return $shiftData;
    }


    /**
     * @return bool
     * @throws \Exception
     */
    public function checkShiftTime(): bool
    {
        $shiftData = $this->getShiftTime();

        if ($shiftData) {
            $currentTS = time();
            $startTS = $shiftData['start_utc_ts'] ?? 0;
            $endTS = $shiftData['end_utc_ts'] ?? 0;

            /* VarDumper::dump($shiftData, 10, true);
            echo $currentTS.' '.date('Y-m-d H:i:s',$currentTS);
            die; */

            if ($startTS <= $currentTS && $endTS >= $currentTS) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return int|string
     * @throws \Exception
     */
    public function getCountNewLeadCurrentShift()
    {
        $shift = $this->getShiftTime();
        // VarDumper::dump($shift, 10, true);
        $endDT = $shift['end_utc_dt'];
        $startDT = $shift['start_period_utc_dt'];
        $query = LeadFlow::find()->where(['>=', 'created', $startDT])->andWhere(['<=', 'created', $endDT])
            ->andWhere(['employee_id' => $this->id, 'lf_from_status_id' => Lead::STATUS_PENDING, 'status' => Lead::STATUS_PROCESSING]);

        // echo $query->createCommand()->getRawSql();

        $count = $query->count();
        return $count;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function accessTakeNewLead(): bool
    {
        $access = false;
        //$shift = $this->getShiftTime();


        if ($params = $this->userParams) {


            if (!$params->up_min_percent_for_take_leads) {
                $access = true;
            } else {

                $currentShiftTaskInfoSummary = $this->getCurrentShiftTaskInfoSummary();
                if ($currentShiftTaskInfoSummary['completedTasksPercent'] >= $params->up_min_percent_for_take_leads) {
                    $access = true;
                } else {
                    $countNewLeads = $this->getCountNewLeadCurrentShift();

                    if (!$params->up_default_take_limit_leads) {
                        $access = true;
                    } elseif ($countNewLeads < $params->up_default_take_limit_leads) {
                        $access = true;
                    }
                }

            }

        }

        return $access;
    }

    /**
     * @return array
     */
    public function accessTakeLeadByFrequencyMinutes(): array
    {
        $access = true;
        $takeDt = new \DateTime();
        $timeZone = $this->userParams->up_timezone ?: 'UTC';

        $params = $this->userParams;
        if($params){
            if($params->up_frequency_minutes){
                $lastTakenDt = $this->getLastTakenLeadDt();

                if(!empty($lastTakenDt)){
                    $lastTakenUTC = new \DateTime($lastTakenDt);
                    $lastTakenUTC->setTimezone(new \DateTimeZone('UTC'));

                    $nowUTC = new \DateTime();
                    $nowUTC->setTimezone(new \DateTimeZone('UTC'));

                    $frequencyMinutes = $params->up_frequency_minutes;

                    $nextTakeUTC = $lastTakenUTC->add(new \DateInterval('PT' . $frequencyMinutes . 'M'));

                    if($nextTakeUTC > $nowUTC){
                        $access = false;
                        $takeDt = $nextTakeUTC;
                    }

                }
            }
        }

        $takeDt->setTimezone(new \DateTimeZone($timeZone));
        $takeDtUTC = $takeDt->setTimezone(new \DateTimeZone('UTC'));

        return ['access' => $access, 'takeDt' => $takeDt, 'takeDtUTC' => $takeDtUTC];
    }

    public static function getAllEmployeesByRole($role = 'agent')
    {
        return self::find()->leftJoin('auth_assignment','auth_assignment.user_id = id')->andWhere(['auth_assignment.item_name' => $role])->all();
    }

    /**
     * @param int $user_id
     * @return array
     */
    public static function getPhoneList(int $user_id) : array
    {
        $phoneList = [];
        $phoneList2 = [];

        $phones = UserProjectParams::find()->select(['DISTINCT(upp_phone_number)'])->where(['upp_user_id' => $user_id])
            ->andWhere(['and', ['<>', 'upp_phone_number', ''], ['IS NOT', 'upp_phone_number', null]])
            ->asArray()->all();

        if($phones) {
            $phoneList = ArrayHelper::map($phones, 'upp_phone_number', 'upp_phone_number');
        }

        $phones2 = UserProjectParams::find()->select(['DISTINCT(upp_tw_phone_number)'])->where(['upp_user_id' => $user_id])
            ->andWhere(['and', ['<>', 'upp_tw_phone_number', ''], ['IS NOT', 'upp_tw_phone_number', null]])
            ->asArray()->all();

        //VarDumper::dump($phones2); //->createCommand()->rawSql);  exit();

        if($phones2) {
            $phoneList2 = ArrayHelper::map($phones2, 'upp_tw_phone_number', 'upp_tw_phone_number');
        }
        $phoneList = array_merge($phoneList, $phoneList2);

        return $phoneList;
    }


    /**
     * @return bool
     */
    public function isOnline() : bool
    {
        $online = UserConnection::find()->where(['uc_user_id' => $this->id])->exists();
        return $online;
    }

    /**
     * @return bool
     */
    public function isCallStatusReady() : bool
    {
        $isReady = true;
        $ucs = UserCallStatus::find()->where(['us_user_id' => $this->id])->orderBy(['us_id' => SORT_DESC])->limit(1)->one();
        if($ucs) {
            if((int) $ucs->us_type_id === UserCallStatus::STATUS_TYPE_OCCUPIED) {
                $isReady = false;
            }
        }
        return $isReady;
    }

    /**
     * @return bool
     */
    public function isCallFree() : bool
    {
        $isFree = true;
        //$call = Call::find()->where(['c_created_user_id' => $this->id])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();

        $callExist = Call::find()->where(['c_created_user_id' => $this->id, 'c_call_status' => [Call::CALL_STATUS_QUEUE, Call::CALL_STATUS_RINGING, Call::CALL_STATUS_IN_PROGRESS]])->limit(1)->exists();

        if($callExist) {
            //if(in_array($call->c_call_type_id, [Call::CALL_STATUS_QUEUE, Call::CALL_STATUS_RINGING, Call::CALL_STATUS_IN_PROGRESS])) {
                $isFree = false;
            //}
        }
        return $isFree;
    }


    /**
     * @param int $user_id
     * @param int $project_id
     * @param int $hours
     * @return UserConnectionQuery
     */
    public static function getQueryAgentOnlineStatus(int $user_id, int $project_id = 0, int $hours = 1) : UserConnectionQuery
    {
        $query = UserConnection::find();
        $date_time = date('Y-m-d H:i:s', strtotime('-' . $hours .' hours'));

        $subQuery2 = UserCallStatus::find()->select(['us_type_id'])->where('us_user_id = user_connection.uc_user_id')->orderBy(['us_id' => SORT_DESC])->limit(1);
        $subQuery3 = Call::find()->select(['c_call_status'])->where('c_created_user_id = user_connection.uc_user_id')->orderBy(['c_id' => SORT_DESC])->limit(1);
        //$subQuery4 = UserProjectParams::find()->select(['upp_tw_sip_id'])->where('upp_user_id = user_connection.uc_user_id')->andWhere(['upp_project_id' => $project_id]);
        $subQuery4 = UserProfile::find()->select(['up_call_type_id'])->where('up_user_id = user_connection.uc_user_id');
        $subQuery6 = UserProjectParams::find()->select(['upp_tw_phone_number'])->where('upp_user_id = user_connection.uc_user_id')->andWhere(['upp_project_id' => $project_id]);



        $subQuery5 = Call::find()->select(['COUNT(*)'])->where('c_created_user_id = user_connection.uc_user_id')->andWhere(['c_call_type_id' => Call::CALL_TYPE_IN])->andWhere(['>=', 'c_created_dt', $date_time]);

        $query->select([
                'tbl_user_id' => 'user_connection.uc_user_id',
                'tbl_call_status_id' => $subQuery2,
                'tbl_last_call_status' => $subQuery3,
                'tbl_call_type_id' => $subQuery4,
                'tbl_phone' => $subQuery6,
                'tbl_calls_count' => $subQuery5
            ]
        );

        $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $user_id]);
        $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
        $query->andWhere(['IN', 'user_connection.uc_user_id', $subQuery]);
        $query->groupBy(['user_connection.uc_user_id']);
        $query->orderBy(['tbl_calls_count' => SORT_ASC]);

        return $query;
    }

    /**
     * @param int $user_id
     * @param int $project_id
     * @return array
     */
    public static function getAgentsForCall(int $user_id, int $project_id) : array
    {

        $subQuery = self::getQueryAgentOnlineStatus($user_id, $project_id, 10);
        $generalQuery = new Query();
        $generalQuery->from(['tbl' => $subQuery]);
        $generalQuery->andWhere(['<>', 'tbl_user_id', $user_id]);
        $generalQuery->andWhere(['OR', ['NOT IN', 'tbl_last_call_status', [Call::CALL_STATUS_RINGING, Call::CALL_STATUS_IN_PROGRESS]], ['tbl_last_call_status' => null]]);
        $generalQuery->andWhere(['OR', ['tbl_call_status_id' => UserCallStatus::STATUS_TYPE_READY], ['tbl_call_status_id' => null]]);
        $generalQuery->andWhere(['AND', ['<>', 'tbl_call_type_id', UserProfile::CALL_TYPE_OFF], ['IS NOT', 'tbl_call_type_id', null]]);
        $generalQuery->orderBy(['tbl_calls_count' => SORT_ASC]);

        //$sqlRaw = $generalQuery->createCommand()->getRawSql();
        //echo $sqlRaw;
        //VarDumper::dump($sqlRaw, 10, true);
        //exit;
        $users = $generalQuery->all();
        return $users;
    }


    /**
     * @param int $user_id
     * @param int|null $supervision_id
     * @return bool
     */
    public static function isSupervisionAgent(int $user_id, int $supervision_id = null) : bool
    {
        //$exist = false;
        if(!$supervision_id) {
            $supervision_id = Yii::$app->user->id;
        }

        $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $supervision_id]);
        $exist = UserGroupAssign::find()->select(['ugs_user_id'])->where(['IN', 'ugs_group_id', $subQuery1])->andWhere(['ugs_user_id' => $user_id])->exists();

        return $exist;
    }

    public static function getAgentsForGeneralLineCall( int $project_id, string $called_phone,  int $hours = 1)
    {
        $query = UserConnection::find();
        $date_time = date('Y-m-d H:i:s', strtotime('-' . $hours .' hours'));

        $subQuery2 = UserCallStatus::find()->select(['us_type_id'])->where('us_user_id = user_connection.uc_user_id')->orderBy(['us_id' => SORT_DESC])->limit(1);
        $subQuery3 = Call::find()->select(['c_call_status'])->where('c_created_user_id = user_connection.uc_user_id')->orderBy(['c_id' => SORT_DESC])->limit(1);
        $subQuery4 = UserProfile::find()->select(['up_call_type_id'])->where('up_user_id = user_connection.uc_user_id');
        $subQuery5 = Call::find()->select(['COUNT(*)'])
            ->where('c_created_user_id = user_connection.uc_user_id')
            ->andWhere(['c_call_type_id' => Call::CALL_TYPE_IN])
            ->andWhere(['c_call_status' => Call::CALL_STATUS_COMPLETED])
            ->andWhere(['c_project_id' => $project_id])
            ->andWhere(['>=', 'c_created_dt', $date_time])
            ->andWhere(['c_to' => $called_phone]);

        $query->select([
                'tbl_user_id' => 'user_connection.uc_user_id',
                'tbl_call_status_id' => $subQuery2,
                'tbl_last_call_status' => $subQuery3,
                'tbl_call_type_id' => $subQuery4,
                'tbl_calls_count' => $subQuery5,
            ]
        );

        $subQuery = ProjectEmployeeAccess::find()->select(['DISTINCT(employee_id)'])->where(['project_id' => $project_id]);
        $query->andWhere(['IN', 'user_connection.uc_user_id', $subQuery]);
        $query->groupBy(['user_connection.uc_user_id']);
        $query->orderBy(['tbl_calls_count' => SORT_ASC]);

        $generalQuery = new Query();
        $generalQuery->from(['tbl' => $query]);
        $generalQuery->andWhere(['OR', ['NOT IN', 'tbl_last_call_status', [Call::CALL_STATUS_RINGING, Call::CALL_STATUS_IN_PROGRESS]], ['tbl_last_call_status' => null]]);
        $generalQuery->andWhere(['OR', ['tbl_call_status_id' => UserCallStatus::STATUS_TYPE_READY], ['tbl_call_status_id' => null]]);
        $generalQuery->andWhere(['AND', ['<>', 'tbl_call_type_id', UserProfile::CALL_TYPE_OFF], ['IS NOT', 'tbl_call_type_id', null]]);
        $generalQuery->orderBy(['tbl_calls_count' => SORT_ASC]);

        //$sqlRaw = $generalQuery->createCommand()->getRawSql();
        //echo '<pre>'; print_r($sqlRaw);  exit;
        //VarDumper::dump($sqlRaw, 10, true); exit;
        $users = $generalQuery->all();
        return $users;
    }
}
