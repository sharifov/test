<?php

namespace common\models\search;

use common\models\Call;
use common\models\Email;
use common\models\ProjectEmployeeAccess;
use common\models\query\EmployeeQuery;
use common\models\search\employee\SortParameters;
use common\models\UserConnection;
use common\models\UserDepartment;
use common\models\UserGroupAssign;
use common\models\UserOnline;
use common\models\UserParams;
use common\models\UserProfile;
use common\models\UserProjectParams;
use modules\shiftSchedule\src\entities\userShiftAssign\UserShiftAssign;
use src\helpers\DateHelper;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChatUserAccess\entity\ClientChatUserAccess;
use src\model\clientChatUserChannel\entity\ClientChatUserChannel;
use src\model\emailList\entity\EmailList;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Employee;
use kartik\daterange\DateRangeBehavior;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use src\auth\Auth;

/**
 * EmployeeSearch represents the model behind the search form of `common\models\Employee`.
 */
class EmployeeSearch extends Employee
{
    public $supervision_id;
    public $userGroupIds = [];
    public $user_group_id;
    public $user_project_id;
    public $user_params_project_id;
    public $userDepartmentIds = [];
    public $assignedShifts = [];
    public $userTimezones = [];
    public $chatChannels = [];
    public $skills = [];
    public $useTelegram;
    public $telegramEnabled;
    public $user_department_id;
    public $experienceMonth;
    public $joinDate;
    public $callReady;
    public $createdRangeTime;
    public $updatedRangeTime;
    public $lastLoginRangeTime;
    public $phoneListId;
    public $grav;

    public $user_call_type_id;

    public $timeStart;
    public $timeEnd;
    public $timeRange;

    public $online;
    public $pageSize;

    public $twoFaEnable;

    public $projectAccessIds = [];
    public $projectParamsIds = [];

    public $chatId;
    public $channelId;
    public $limit;
    public $exceptUserId;

    public $show_fields = [];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'acl_rules_activated', 'supervision_id', 'user_group_id', 'user_project_id', 'user_params_project_id', 'online', 'user_call_type_id', 'user_department_id',
                'experienceMonth', 'e_created_user_id', 'e_updated_user_id', 'phoneListId'], 'integer'],
            [['username', 'nickname', 'full_name', 'auth_key', 'password_hash', 'password_reset_token', 'email', 'last_activity', 'pageSize', 'chatChannels', 'phoneListId', 'show_fields'], 'safe'],
            [['timeStart', 'timeEnd', 'roles', 'twoFaEnable', 'joinDate', 'userGroupIds', 'userDepartmentIds', 'assignedShifts', 'skills', 'useTelegram', 'userTimezones', 'telegramEnabled'], 'safe'],
            [['joinDate'], 'date', 'format' => 'php:Y-m-d', 'skipOnEmpty' => true],
            [['timeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['projectParamsIds', 'projectAccessIds', 'assignedShifts', 'userDepartmentIds', 'userGroupIds', 'skills', 'chatChannels'], 'each', 'rule' => ['integer']],
            [['created_at', 'updated_at'], 'date', 'format' => 'php:Y-m-d'],
            ['acl_rules_activated', 'in', 'range' => [0, 1]],
            ['useTelegram', 'in', 'range' => [0, 1]],
            ['callReady', 'in', 'range' => [0, 1]],
            ['telegramEnabled', 'in', 'range' => [0, 1]],
            ['skills', 'each', 'rule' => ['in', 'range' => array_keys(UserProfile::SKILL_TYPE_LIST)]],
            ['userTimezones', 'each', 'rule' => ['in', 'range' => UserParams::getActiveTimezones()]],
            [['createdRangeTime', 'updatedRangeTime', 'lastLoginRangeTime'], 'convertDateTimeRange'],
            ['show_fields', 'filter', 'filter' => static function ($value) {
                return is_array($value) ? $value : [];
            }, 'skipOnEmpty' => true],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Employee::find()->with('ugsGroups', 'userParams', 'userProfile', 'userProjectParams.phoneList');

        // add conditions that should always apply here
        //VarDumper::dump($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => isset($params['per-page']) ? (int)$params['per-page'] : 20,
            ],
        ]);

        //$dataProvider->pagination->pageSize = ($this->pageSize !== NULL) ? $this->pageSize : 40;

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        $this->filterQuery($query);

        return $dataProvider;
    }


    /**
     * @param $params
     * @return array
     */
    public function searchIds($params): array
    {
        $query = Employee::find()->with('ugsGroups', 'userParams', 'userProfile', 'userProjectParams.phoneList');

        $this->load($params);
        if (!$this->validate()) {
            $query->where('0=1');
            return [];
        }
        $query->select('employees.id');
        $this->filterQuery($query);

        return ArrayHelper::map($query->asArray()->all(), 'id', 'id');
    }


    /**
     * @param EmployeeQuery $query
     */
    private function filterQuery(EmployeeQuery $query)
    {
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'last_activity' => $this->last_activity,
            'acl_rules_activated' => $this->acl_rules_activated,
            'e_created_user_id' => $this->e_created_user_id,
            'e_updated_user_id' => $this->e_updated_user_id,
        ]);

        if ($this->updated_at) {
            $query->andFilterWhere(['>=', 'updated_at', Employee::convertTimeFromUserDtToUTC(strtotime($this->updated_at))])
                ->andFilterWhere(['<=', 'updated_at', Employee::convertTimeFromUserDtToUTC(strtotime($this->updated_at) + 3600 * 24)]);
        }

        if ($this->roles) {
            $query->andWhere(['IN', 'employees.id', array_keys(Employee::getListByRole($this->roles))]);
        }

        if ($this->userGroupIds) {
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['ugs_group_id' => $this->userGroupIds]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        if ($this->userDepartmentIds) {
            $subQuery = UserDepartment::find()->usersByDep($this->userDepartmentIds);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        if ($this->assignedShifts) {
            $subQuery = UserShiftAssign::find()->select(['DISTINCT(usa_user_id)'])->andWhere(['usa_sh_id' => $this->assignedShifts]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        if ($this->skills) {
            $subQuery = UserProfile::find()->select(['DISTINCT(up_user_id)'])->andWhere(['up_skill' => $this->skills]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        if (ArrayHelper::isIn($this->useTelegram, ['1', '0'], false)) {
            $query->leftJoin('user_profile', 'employees.id = user_profile.up_user_id');

            if ($this->useTelegram == 0) {
                $query->andWhere(['or',
                    ['user_profile.up_telegram' => ''],
                    ['is', 'user_profile.up_telegram', new \yii\db\Expression('null')]]);
            } else {
                $query->andWhere(['!=', 'user_profile.up_telegram', '']);
            }
        }

        if (ArrayHelper::isIn($this->telegramEnabled, ['1', '0'], false)) {
            $query->leftJoin('user_profile', 'employees.id = user_profile.up_user_id');

            if ($this->telegramEnabled == 0) {
                $query->andWhere(['or',
                    ['user_profile.up_telegram_enable' => 0],
                    ['is', 'user_profile.up_telegram_enable', new \yii\db\Expression('null')]]);
            } else {
                $query->andWhere(['user_profile.up_telegram_enable' => 1]);
            }
        }

        if ($this->userTimezones) {
            $subQuery = UserParams::find()->select(['DISTINCT(up_user_id)'])->andWhere(['up_timezone' => $this->userTimezones]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        if ($this->chatChannels) {
            $subQuery = ClientChatUserChannel::find()->select(['DISTINCT(ccuc_user_id)'])->andWhere(['ccuc_channel_id' => $this->chatChannels]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        if ($this->createdRangeTime) {
            $createdRange = explode(" - ", $this->createdRangeTime);
            if ($createdRange[0]) {
                $query->andFilterWhere(['>=', 'employees.created_at', Employee::convertTimeFromUserDtToUTC(strtotime($createdRange[0]))]);
            }
            if ($createdRange[1]) {
                $query->andFilterWhere(['<=', 'employees.created_at', Employee::convertTimeFromUserDtToUTC(strtotime($createdRange[1]))]);
            }
        }

        if ($this->updatedRangeTime) {
            $updatedRange = explode(" - ", $this->updatedRangeTime);
            if ($updatedRange[0]) {
                $query->andFilterWhere(['>=', 'employees.updated_at', Employee::convertTimeFromUserDtToUTC(strtotime($updatedRange[0]))]);
            }
            if ($updatedRange[1]) {
                $query->andFilterWhere(['<=', 'employees.updated_at', Employee::convertTimeFromUserDtToUTC(strtotime($updatedRange[1]))]);
            }
        }

        if ($this->lastLoginRangeTime) {
            $lastLogin = explode(" - ", $this->lastLoginRangeTime);
            if ($lastLogin[0]) {
                $query->andFilterWhere(['>=', 'employees.last_login_dt', Employee::convertTimeFromUserDtToUTC(strtotime($lastLogin[0]))]);
            }
            if ($lastLogin[1]) {
                $query->andFilterWhere(['<=', 'employees.last_login_dt', Employee::convertTimeFromUserDtToUTC(strtotime($lastLogin[1]))]);
            }
        }

        if ($this->phoneListId) {
            $subQuery = UserProjectParams::find()->select(['DISTINCT(upp_user_id)'])->andWhere(['upp_phone_list_id' => $this->phoneListId]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        if ($this->user_params_project_id > 0) {
            $subQuery = UserProjectParams::find()->select(['DISTINCT(upp_user_id)'])->where(['=', 'upp_project_id', $this->user_params_project_id]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        if ($this->projectParamsIds) {
            $subQuery = UserProjectParams::find()->select(['DISTINCT(upp_user_id)'])->where(['upp_project_id' => $this->projectParamsIds]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        if ($this->user_call_type_id > 0 || $this->user_call_type_id === '0') {
            $subQuery = UserProfile::find()->select(['DISTINCT(up_user_id)'])->where(['=', 'up_call_type_id', $this->user_call_type_id]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        if (strlen($this->twoFaEnable) > 0) {
            $subQuery = UserProfile::find()->select(['DISTINCT(up_user_id)'])->where(['=', 'up_2fa_enable', $this->twoFaEnable]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }


        if ($this->projectAccessIds) {
            $subQuery = ProjectEmployeeAccess::find()->select(['DISTINCT(employee_id)'])->where(['project_id' => $this->projectAccessIds]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        if ($this->user_project_id > 0) {
            $subQuery = ProjectEmployeeAccess::find()->select(['DISTINCT(employee_id)'])->where(['=', 'project_id', $this->user_project_id]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        if ($this->supervision_id > 0) {
            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        if ($this->online > 0) {
            if ($this->online == 1) {
                $subQuery = UserOnline::find()->select(['uo_user_id']);
                $query->andWhere(['IN', 'employees.id', $subQuery]);
            } elseif ($this->online == 2) {
                $subQuery = UserOnline::find()->select(['uo_user_id']);
                $query->andWhere(['NOT IN', 'employees.id', $subQuery]);
            }
        }

        if ($this->experienceMonth > 0) {
            $subQuery = UserProfile::find()->select(['DISTINCT(up_user_id)'])->where(['=', 'ABS(TIMESTAMPDIFF(MONTH, curdate(), up_join_date))', $this->experienceMonth]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        if (!empty($this->joinDate)) {
            $subQuery = UserProfile::find()->select(['DISTINCT(up_user_id)'])->where(['=', 'up_join_date', $this->joinDate]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        if (ArrayHelper::isIn($this->callReady, ['1', '0'], false)) {
            $query->leftJoin('user_status', 'employees.id = user_status.us_user_id');
            if ($this->callReady == 0) {
                $query->andWhere(['or', ['user_status.us_call_phone_status' => $this->callReady],
                    ['is', 'user_status.us_call_phone_status', new \yii\db\Expression('null')]]);
            } else {
                $query->andWhere(['user_status.us_call_phone_status' => $this->callReady]);
            }
        }

        if ($this->email) {
            $subQuery = UserProjectParams::find()->select(['DISTINCT(upp_user_id)'])
                ->andWhere(['IN', 'upp_email_list_id', EmailList::find()->select('el_id')
                    ->andWhere(['like', 'el_email', $this->email])
                    ->orWhere(['like', 'el_title', $this->email])
                ]);
            $query->andWhere([
                'or',
                ['like', 'email', $this->email],
                ['IN', 'employees.id', $subQuery]
            ]);
        }

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'nickname', $this->nickname])
            ->andFilterWhere(['like', 'full_name', $this->full_name])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token]);
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchByUserGroups($params)
    {
        $query = Employee::find()->with(['ugsGroups']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'last_activity' => $this->last_activity,
            'acl_rules_activated' => $this->acl_rules_activated,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ]);

        if ($this->user_group_id > 0) {
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['=', 'ugs_group_id', $this->user_group_id]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        if ($this->user_department_id > 0) {
            $subQuery = UserDepartment::find()->usersByDep($this->user_department_id);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        if ($this->supervision_id > 0) {
            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'full_name', $this->full_name])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token]);


        /*$query->andFilterWhere(['>=', 'createdAt', $this->timeStart])
            ->andFilterWhere(['<', 'createdAt', $this->timeEnd]);*/

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ArrayDataProvider
     * @throws \yii\db\Exception
     */
    public function searchByUserGroupsForSupervision(array $params): ArrayDataProvider
    {
        $query = Employee::find()->select(['id', 'username', 'status', 'auth_assignment.item_name'])->leftJoin('auth_assignment', 'id = user_id');

        $this->load($params);

        /*if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }*/

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id
        ]);

        if ($this->user_group_id > 0) {
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['=', 'ugs_group_id', $this->user_group_id]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        if ($this->supervision_id > 0) {
            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
            $query->andWhere(['IN', 'employees.id', $subQuery]);
        }

        $query->andFilterWhere(['like', 'username', $this->username]);

        $command = $query->createCommand();
        $data = $command->queryAll();

        $newModels = [];
        $filteredUserIds = [];
        foreach ($data as $key => $model) {
            if (Auth::user()->isSupervision() && $model['item_name'] == Employee::ROLE_AGENT && !in_array($model['id'], $filteredUserIds)) {
                $newModels[] = $model;
                array_push($filteredUserIds, $model['id']);
            } elseif (Auth::user()->isExSuper() && $model['item_name'] == Employee::ROLE_EX_AGENT && !in_array($model['id'], $filteredUserIds)) {
                $newModels[] = $model;
                array_push($filteredUserIds, $model['id']);
            } elseif (Auth::user()->isSupSuper() && $model['item_name'] == Employee::ROLE_SUP_AGENT && !in_array($model['id'], $filteredUserIds)) {
                $newModels[] = $model;
                array_push($filteredUserIds, $model['id']);
            } elseif ($model['id'] == Auth::id() && !in_array($model['id'], $filteredUserIds)) {
                $newModels[] = $model;
                array_push($filteredUserIds, $model['id']);
            }
        }

        $paramsData = [
            'allModels' => $newModels,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => [
                    'id',
                    'username',
                ],
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ];

        //var_dump($query->createCommand()->getSql()); die();
        return $dataProvider = new ArrayDataProvider($paramsData);
    }

    public function searchAgentLeads($params)
    {
        $this->load($params);

        $query = new Query();
        $query->select(['e.id', 'e.username']);

        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status=' . Lead::STATUS_SOLD . ') AS st_sold ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status=' . Lead::STATUS_ON_HOLD . ') AS st_on_hold ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status=' . Lead::STATUS_PROCESSING . ') AS st_processing ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status=' . Lead::STATUS_FOLLOW_UP . ') AS st_follow_up ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status=' . Lead::STATUS_TRASH . ') AS st_trash ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status=' . Lead::STATUS_REJECT . ') AS st_reject ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status=' . Lead::STATUS_BOOKED . ') AS st_booked ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status=' . Lead::STATUS_SNOOZE . ') AS st_snooze ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status=' . Lead::STATUS_PENDING . ') AS st_pending ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id) AS all_statuses']);


        //$query->from('leads AS l');
        $query->from('employees AS e');

        //$query->where(['IS NOT', 'l.employee_id', null]);
        //$query->where(['>', 'all_statuses', 0]);

        // $query->leftJoin(['employee as e', 'l.employee_id=e.id']);

        //$query->andFilterWhere(['l.status' => [Lead::STATUS_PROCESSING, Lead::STATUS_PENDING, Lead::STATUS_FOLLOW_UP, Lead::STATUS_ON_HOLD]]);

        /*if($this->request_ip) {
            $query->andFilterWhere(['like', 'l.request_ip', $this->request_ip]);
        }*/

        //$query->groupBy(['l.status', 'l.employee_id']);
        $query->having(['>', 'all_statuses', 0]);

        $totalCount = 20;

        $command = $query->createCommand();
        $sql = $command->rawSql;

        $paramsData = [
            'sql' => $sql,
            //'params' => [':publish' => 1],
            //'totalCount' => $totalCount,
            'sort' => [
                'defaultOrder' => ['st_sold' => SORT_DESC],
                'attributes' => [
                    'st_sold' => [
                        'asc' => ['st_sold' => SORT_ASC],
                        'desc' => ['st_sold' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Sold',
                    ],
                    'st_on_hold' => [
                        'asc' => ['st_sst_on_holdold' => SORT_ASC],
                        'desc' => ['st_on_hold' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Hold',
                    ],
                    'st_processing' => [
                        'asc' => ['st_processing' => SORT_ASC],
                        'desc' => ['st_processing' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Processing',
                    ],
                    'st_follow_up' => [
                        'asc' => ['st_follow_up' => SORT_ASC],
                        'desc' => ['st_follow_up' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Follow up',
                    ],
                    'st_trash' => [
                        'asc' => ['st_trash' => SORT_ASC],
                        'desc' => ['st_trash' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Trash',
                    ],
                    'st_reject' => [
                        'asc' => ['st_reject' => SORT_ASC],
                        'desc' => ['st_reject' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Reject',
                    ],
                    'st_booked' => [
                        'asc' => ['st_booked' => SORT_ASC],
                        'desc' => ['st_booked' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Booked',
                    ],
                    'st_snooze' => [
                        'asc' => ['st_snooze' => SORT_ASC],
                        'desc' => ['st_snooze' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Snooze',
                    ],
                    'st_pending' => [
                        'asc' => ['st_pending' => SORT_ASC],
                        'desc' => ['st_pending' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Pending',
                    ],

                    'id' => [
                        'asc' => ['id' => SORT_ASC],
                        'desc' => ['id' => SORT_DESC],
                        'label' => 'Agent',
                    ],
                    'username' => [
                        'asc' => ['username' => SORT_ASC],
                        'desc' => ['username' => SORT_DESC],
                        'label' => 'Agent',
                    ],

                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ];

        $dataProvider = new SqlDataProvider($paramsData);
        return $dataProvider;
    }

    /**
     * @param ClientChat $chat
     * @param int $limit
     * @param array $sortParameters
     * @return Employee[]
     */
    public function searchAvailableAgentsForChatRequests(ClientChat $chat, int $limit, array $sortParameters): array
    {
        $users = self::find()
            ->joinChatUserChannel($chat->cch_channel_id)
            ->online('ccuc_user_id')
            ->registeredInRc();

        if (!$chat->hasOwner() && $chat->isPending()) {
            $subQuery = ClientChatUserAccess::find()->select(['ccua_user_id'])->where(['ccua_cch_id' => $chat->cch_id]);
            $users->andWhere(['NOT IN', 'id', $subQuery]);
        }

        if ($chat->isTransfer() || $chat->isIdle()) {
            $subQuery = ClientChatUserAccess::find()
                ->select(['ccua_user_id as ccua_uid'])
                ->where(['ccua_cch_id' => $chat->cch_id])
                ->andWhere(['ccua_status_id' => [ClientChatUserAccess::STATUS_PENDING]]);
            $users->andWhere(['NOT IN', 'id', $subQuery]);
        }

        $users->hasPermission('client-chat/accept-pending');

        if ($limit) {
            $users->limit($limit);

            if ($sortParameters) {
                $sortParameters = new SortParameters($sortParameters);
                $sortParameters->sortByPriority()->apply($users);
            }
        }

        if ($chat->hasOwner()) {
            $users->exceptUser($chat->cch_owner_user_id);
        }

        return $users->all();
    }

    /**
     * @return array
     */
    public function getPhoneListNumber(): array
    {
        return $this->phoneListId ? [
            $this->phoneListId => \src\model\phoneList\entity\PhoneList::find()->select('pl_phone_number')->andWhere(['pl_id' => $this->phoneListId])->scalar()
        ] : [];
    }


    /**
     * @return string[]
     */
    public function getViewFields(): array
    {
        return [
            'user_call_type_id' => 'Call type',
            'callReady' => 'Call Ready',
            'user_params_project_id' => 'Projects Params',
            'acl_rules_activated' => 'IP filter',
            'userTimezones' => 'Timezone',
            'skills' => 'Skill',
            'assignedShifts' => 'User Shifts',
            'chatChannels' => 'Client Chat Channels',
            'e_created_user_id' => 'Created User',
            'e_updated_user_id' => 'Updated User',
            'created_at' => 'Created DateTime',
            'updated_at' => 'Updated DateTime',
            'last_login_dt' => 'Last Login DateTime',
            'useTelegram' => 'Use Telegram',
            'telegramEnabled' => 'Telegram Enabled',
            'grav' => 'Grav',
        ];
    }

    public function convertDateTimeRange($attribute)
    {
        if ($this->{$attribute}) {
            $date = explode(' - ', $this->{$attribute});
            if (count($date) === 2) {
                if (!DateHelper::checkDateTime($date[0], 'd-M-Y')) {
                    $this->addError($attribute, 'Date time start incorrect format');
                }
                if (!DateHelper::checkDateTime($date[1], 'd-M-Y')) {
                    $this->addError($attribute, 'Date time end incorrect format');
                }
            } else {
                $this->addError($attribute, 'Range Time is not parsed correctly');
            }
        }
    }
}
