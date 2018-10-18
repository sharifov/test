<?php

namespace common\models;

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
 * @property EmployeeProfile[] $employeeProfiles
 * @property Lead[] $leads
 * @property EmployeeAcl[] $employeeAcl
 * @property ProjectEmployeeAccess[] $projectEmployeeAccesses
 * @property UserGroupAssign[] $userGroupAssigns
 * @property UserGroup[] $ugsGroups
 * @property UserParams $userParams
 */
class Employee extends \yii\db\ActiveRecord implements IdentityInterface
{
    const SCENARIO_REGISTER = 'register';

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    const PROFIT_BONUSES = [
        11000   =>  500,
        8000    =>  300,
        5000    =>  150
    ];

    public $password;
    public $deleted;
    public $role;
    public $employeeAccess;
    public $viewItemsEmployeeAccess;
    public $user_groups;

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
            [['username', 'auth_key', 'password_hash', 'email', 'role'], 'required'],
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
            [['created_at', 'updated_at', 'last_activity', 'acl_rules_activated', 'full_name', 'user_groups'], 'safe'],
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

    public function afterFind()
    {
        parent::afterFind();

        //var_dump(\webapi\models\ApiUser::class); die;


        if(isset(Yii::$app->user)) {
            if(Yii::$app->user && Yii::$app->user->identityClass === \webapi\models\ApiUser::class) {
                $this->role = null;
            } else {
                $roles = $this->getRoles();
                $this->role = array_keys($roles)[0] ?? 'noname';
            }
        }

        $this->deleted = !($this->status);

        if ($this->role != 'admin') {
            $this->employeeAccess = array_keys(ArrayHelper::map($this->projectEmployeeAccesses, 'project_id', 'project_id'));
        }
    }

    /*public function afterValidate()
    {
        parent::afterValidate();

        $this->updated_at = date('Y-m-d H:i:s');
    }*/

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

    public static function getAllRoles()
    {
        $roles = [];
        $query = new Query();
        $result = $query->select(['name', 'description'])
            ->from('auth_item')->where(['type' => 1])
            ->all();
        foreach ($result as $item) {
            if ($item['name'] == 'admin' && Yii::$app->user->identity->role != 'admin') {
                continue;
            }
            $roles[$item['name']] = $item['description'];
        }
        return $roles;
    }



    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeeProfiles()
    {
        return $this->hasMany(EmployeeProfile::class, ['employee_id' => 'id']);
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
        if (isset($attr['role'])) {
            $this->role = $attr['role'];
        }
        $this->generateAuthKey();

        return $this->isNewRecord;
    }

    public function addRole($isNew)
    {
        // the following three lines were added:
        $auth = \Yii::$app->authManager;
        if (!$isNew) {
            $auth->revokeAll($this->id);
        }

        $authorRole = $auth->getRole($this->role);
        $auth->assign($authorRole, $this->id);
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

    public function getRoles()
    {
        return ArrayHelper::map(Yii::$app->authManager->getRolesByUser($this->id), 'name', 'description');
    }

    /**
     * @return array
     */
    public static function getList() : array
    {
        $data = self::find()->orderBy(['username' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data,'id', 'username');
    }


    /**
     * @param int|null $user_id
     * @return array
     */
    public static function getListByUserId(int $user_id = null) : array
    {
        $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $user_id]);
        $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);

        $query = self::find()->orderBy(['username' => SORT_ASC])->asArray();
        $query->andWhere(['IN', 'employees.id', $subQuery]);

        $data = $query->all();
        return ArrayHelper::map($data,'id', 'username');
    }



    /**
     * @return array
     */
    public function getListByProject($projectId) : array
    {
        if(Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id))
        {
            $data = self::find()
            ->orderBy(['username' => SORT_ASC])
            ->asArray()->all();
        }else{
            $data = self::find()
            ->joinWith('projectEmployeeAccesses')
            ->where(['=','project_id',$projectId])
            ->orderBy(['username' => SORT_ASC])
            ->asArray()->all();
        }

        return ArrayHelper::map($data,'id', 'username');
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
    public function getUserGroupList() : array
    {
        $groups = [];
        if( $groupsModel =  $this->ugsGroups) {
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
     * @param string|null $start_dt
     * @param string|null $end_dt
     * @return string
     */
    public function getTaskStats(string $start_dt = null, string $end_dt = null): string
    {

        if($start_dt) {
            $start_dt = date('Y-m-d', strtotime($start_dt));
        } else {
            $start_dt = null;
        }

        if($end_dt) {
            $end_dt = date('Y-m-d', strtotime($end_dt));
        } else {
            $end_dt = null;
        }


        $taskListAllQuery = \common\models\LeadTask::find()->select(['COUNT(*) AS field_cnt', 'lt_task_id'])
            ->where(['lt_user_id' => $this->id])
            ->andFilterWhere(['>=', 'lt_date', $start_dt])
            ->andFilterWhere(['<=', 'lt_date', $end_dt])
            ->groupBy(['lt_task_id']);

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
        if($taskListChecked) {
            foreach ($taskListChecked as $taskItem) {
                $completed[$taskItem->lt_task_id] = $taskItem->field_cnt;
            }
        }

        //$itemHeader = [];
        $item = [];

        if($taskListAll) {
            foreach ($taskListAll as $task) {
                //$itemHeader[] = Html::encode($task->ltTask->t_name);
                //$item[] = ''.($completed[$task->lt_task_id] ?? 0).'/'. $task->field_cnt.'';

                $completedTasks = $completed[$task->lt_task_id] ?? 0;

                $str = '<b>'.Html::encode($task->ltTask->t_name).'</b><br>'.$completedTasks.' / '. Html::a($task->field_cnt,
                        ['lead-task/index', 'LeadTaskSearch[lt_task_id]' => $task->lt_task_id, 'LeadTaskSearch[lt_user_id]' => $this->id],
                        ['data-pjax' => 0, 'target' => '_blank']).'';


                if($task->field_cnt > 0) {
                    $percent = (int) ($completedTasks * 100 / $task->field_cnt);
                } else {
                    $percent = 0;
                }

                $str .= '<br><div class="progress" title="'.$percent.'%">
                          <div class="progress-bar" role="progressbar" aria-valuenow="'.$percent.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$percent.'%;">
                            '.$percent.'%
                          </div>
                        </div>';


                $item[] = $str;
            }
        }


        if($item) {
            $str = '<table class="table table-bordered table-condensed">';
            //$str .= '<tr><th class="text-center">'.implode('</th><th class="text-center">', $itemHeader).'</th></tr>';

            $str .= '<tr>';
            $str .= '<td class="text-center">' . implode('</td><td class="text-center">', $item) . '</td>';
            $str .= '</tr>';
            $str .= '</table>';
        } else {
            $str = '-';
        }

        return $str;
    }

    /*
     * @param startDate DateTime
     * @param endDat DateTime
     *
     * */
    public function calculateSalaryBetween($startDate, $endDate)
    {
        $base = ($this->userParams)?$this->userParams->up_base_amount:200;
        $commission = ($this->userParams)?$this->userParams->up_commission_percent:10;
        $bonusActive = ($this->userParams)?$this->userParams->up_bonus_active:1;
        $bonus = 0;

        $query = new Query();
        $query->select([
            'lead_id' => 'l.id',
            'q_id' => 'q.id',
            'selling' => 'SUM(qp.selling)',
            'mark_up' => 'SUM(qp.mark_up + qp.extra_mark_up)',
            'fare_type' => 'q.fare_type',
            'check_payment' => 'q.check_payment',
            'minus_percent' => 'SUM(ps.ps_percent)',
            'agent_type' => "(CASE WHEN l.employee_id = $this->id THEN 'main' ELSE 'split' END)",
            'split_percent' => "SUM(CASE WHEN ps.ps_user_id = $this->id THEN ps.ps_percent ELSE 0 END)"
        ])
        ->from(Lead::tableName().' l')
        ->leftJoin(Quote::tableName().' q','q.lead_id = l.id')
        ->leftJoin(QuotePrice::tableName().' qp','q.id = qp.quote_id')
        ->leftJoin(ProfitSplit::tableName().' ps','ps.ps_lead_id = l.id')
        ->where(['l.status' => Lead::STATUS_SOLD, 'q.status' => Quote::STATUS_APPLIED])
        ->andWhere('l.employee_id = '.$this->id.' OR ps.ps_user_id = '.$this->id)
        ->groupBy(['q.id','l.id'])
        ;

        if($startDate !== null || $endDate !== null) {
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
        foreach ($res as $entry){
            $entry['minus_percent'] = intval($entry['minus_percent']);
            $totalProfit = Quote::getProfit($entry['mark_up'], $entry['selling'], $entry['fare_type'], $entry['check_payment']);
            if($entry['agent_type'] == 'main'){
                $agentProfit = $totalProfit*(100-$entry['minus_percent'])/100;
            }else{
                $agentProfit = $totalProfit*$entry['split_percent']/100;
            }
            $profit += $agentProfit;
        }

        if($bonusActive){
            $profitBonuses = $this->getProfitBonuses();
            if(empty($profitBonuses)){
                $profitBonuses = self::PROFIT_BONUSES;
            }
            foreach ($profitBonuses as $profKey => $bonusVal) {
                if($profit >= $profKey){
                    $bonus = $bonusVal;
                    break;
                }
            }
        }

        $profit = $profit * $commission/100;

        return [
            'salary' => $profit + $base + $bonus,
            'base' => $base,
            'bonus' => $bonus,
            'commission' => $commission,
            ];
    }


    /**
     * @param array $statusList
     * @param string|null $startDate
     * @param string|null $endDate
     * @return int
     */
    public function getLeadCountByStatus(array $statusList = [], string $startDate = null, string $endDate = null) : int
    {
        if($startDate) {
            $startDate = date('Y-m-d', strtotime($startDate));
        }

        if($endDate) {
            $endDate = date('Y-m-d', strtotime($endDate));
        }

        $query = LeadFlow::find()->select('COUNT(DISTINCT(lead_id))')->where(['employee_id' => $this->id, 'status' => $statusList]);
        $query->andFilterWhere(['>=', 'created', $startDate]);
        $query->andFilterWhere(['<=', 'created', $endDate]);
        $count = $query->asArray()->scalar();
        return $count;
    }

    public function getProfitBonuses()
    {
        $pb = ProfitBonus::find()->where(['pb_user_id' => $this->id])->orderBy(['pb_min_profit' => SORT_DESC])->all();
        $data = [];
        foreach ($pb as $entry){
            $data[$entry['pb_min_profit']] = $entry['pb_bonus'];
        }
        return $data;
    }
}
