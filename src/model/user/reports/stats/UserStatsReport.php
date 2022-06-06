<?php

namespace src\model\user\reports\stats;

use common\components\validators\IsArrayValidator;
use common\models\Employee;
use common\models\Lead;
use common\models\LeadFlow;
use common\models\ProfitSplit;
use common\models\Project;
use common\models\ProjectEmployeeAccess;
use common\models\TipsSplit;
use common\models\UserDepartment;
use common\models\UserGroup;
use common\models\UserGroupAssign;
use common\models\UserParams;
use src\helpers\query\QueryHelper;
use src\model\leadUserConversion\entity\LeadUserConversion;
use src\model\userData\entity\UserData;
use src\model\userData\entity\UserDataKey;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\validators\DateValidator;

/**
 * Class UserStatsReport
 *
 * @property $timeZone
 * @property $dateRange
 * @property $dateFrom
 * @property $dateTo
 * @property $departments
 * @property $roles
 * @property $groups
 * @property $user
 * @property $groupBy
 * @property $metrics
 * @property $isValid
 * @property Access $access
 */
class UserStatsReport extends Model
{
    public const GROUP_BY_USER_NAME = 1;
    public const GROUP_BY_USER_GROUP = 2;
    public const GROUP_BY_USER_ROLE = 3;
    public const GROUP_BY_LIST = [
        self::GROUP_BY_USER_NAME => 'User Name',
        self::GROUP_BY_USER_GROUP => 'User Group',
        self::GROUP_BY_USER_ROLE => 'User Role',
    ];

    public $timeZone;

    public $dateRange;
    public $dateFrom;
    public $dateTo;

    public $departments;
    public $roles;
    public $groups;
    public $user;
    public $groupBy;
    public $metrics;
    public $project;

    public $isValid = false;

    private Access $access;
    private array $summaryStats = [];

    public function rules(): array
    {
        return [
            ['groupBy', 'required'],
            ['groupBy', 'integer'],
            ['groupBy', 'filter', 'filter' => 'intval', 'skipOnError' => true],
            ['groupBy', 'in', 'range' => array_keys(self::GROUP_BY_LIST)],

            ['timeZone', 'required'],
            ['timeZone', 'string'],
            ['timeZone', 'in', 'range' => array_keys(Employee::timezoneList(true))],

            ['dateRange', 'required'],
            ['dateRange', 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            ['dateRange', 'validateRange', 'params' => ['minStartFrom' => '2018-01-01 00:00', 'maxEndTo' => date("Y-m-d 23:59")]],

            ['departments', IsArrayValidator::class],
            ['departments', 'each', 'rule' => ['in', 'range' => array_keys($this->getDepartmentList())], 'skipOnError' => true, 'skipOnEmpty' => true],

            ['roles', IsArrayValidator::class],
            ['roles', 'each', 'rule' => ['in', 'range' => array_keys($this->getRolesList())], 'skipOnError' => true, 'skipOnEmpty' => true],

            ['groups', IsArrayValidator::class],
            ['groups', 'each', 'rule' => ['in', 'range' => array_keys($this->getGroupList())], 'skipOnError' => true, 'skipOnEmpty' => true],

            ['user', 'integer'],
            ['user', 'in', 'range' => array_keys($this->getUsersList())],

            ['metrics', 'required'],
            ['metrics', IsArrayValidator::class],
            ['metrics', 'each', 'rule' => ['in', 'range' => array_keys($this->getMetricsList())], 'skipOnError' => true, 'skipOnEmpty' => true],

            ['project', IsArrayValidator::class],
            ['project', 'each', 'rule' => ['in', 'range' => array_keys(Project::getList())], 'skipOnError' => true, 'skipOnEmpty' => true],
        ];
    }

    public function __construct(
        string $defaultTimeZone,
        string $defaultDateRange,
        Access $access,
        $config = []
    ) {
        parent::__construct($config);
        $this->timeZone = $defaultTimeZone;
        $this->dateRange = $defaultDateRange;
        $this->access = $access;
    }

    public function search(array $params)
    {
        $query = new Query();
        $query->select([
            'users.id',
            'users.username',
        ]);
        $query->from(['users' => Employee::tableName()]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'username' => SORT_ASC,
                ],
                'attributes' => [
                    'conversion_percent' => [
                        'asc' => ['conversion_percent' => SORT_ASC],
                        'desc' => ['conversion_percent' => SORT_DESC],
                        'default' => SORT_DESC,
                    ],
                    'sold_leads' => [
                        'asc' => ['sold_leads' => SORT_ASC],
                        'desc' => ['sold_leads' => SORT_DESC],
                        'default' => SORT_DESC,
                    ],
                    'split_share' => [
                        'asc' => ['split_share' => SORT_ASC],
                        'desc' => ['split_share' => SORT_DESC],
                        'default' => SORT_DESC,
                    ],
                    'qualified_leads_taken' => [
                        'asc' => ['qualified_leads_taken' => SORT_ASC],
                        'desc' => ['qualified_leads_taken' => SORT_DESC],
                        'default' => SORT_DESC,
                    ],
                    'gross_profit' => [
                        'asc' => ['gross_profit' => SORT_ASC],
                        'desc' => ['gross_profit' => SORT_DESC],
                        'default' => SORT_DESC,
                    ],
                    'tips' => [
                        'asc' => ['tips' => SORT_ASC],
                        'desc' => ['tips' => SORT_DESC],
                        'default' => SORT_DESC,
                    ],
                    'leads_created' => [
                        'asc' => ['leads_created' => SORT_ASC],
                        'desc' => ['leads_created' => SORT_DESC],
                        'default' => SORT_DESC,
                    ],
                    'leads_processed' => [
                        'asc' => ['leads_processed' => SORT_ASC],
                        'desc' => ['leads_processed' => SORT_DESC],
                        'default' => SORT_DESC,
                    ],
                    'leads_trashed' => [
                        'asc' => ['leads_trashed' => SORT_ASC],
                        'desc' => ['leads_trashed' => SORT_DESC],
                        'default' => SORT_DESC,
                    ],
                    'leads_follow_up' => [
                        'asc' => ['leads_follow_up' => SORT_ASC],
                        'desc' => ['leads_follow_up' => SORT_DESC],
                        'default' => SORT_DESC,
                    ],
                    'leads_cloned' => [
                        'asc' => ['leads_cloned' => SORT_ASC],
                        'desc' => ['leads_cloned' => SORT_DESC],
                        'default' => SORT_DESC,
                    ],
                    'group_name' => [
                        'asc' => ['group_name' => SORT_ASC],
                        'desc' => ['group_name' => SORT_DESC],
                        'default' => SORT_ASC,
                    ],
                    'role_name' => [
                        'asc' => ['role_name' => SORT_ASC],
                        'desc' => ['role_name' => SORT_DESC],
                        'default' => SORT_ASC,
                    ],
                    'username' => [
                        'asc' => ['username' => SORT_ASC],
                        'desc' => ['username' => SORT_DESC],
                        'default' => SORT_ASC,
                    ],
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $this->isValid = false;
            $query->where('0=1');

            if (!\Yii::$app->request->isPost) {
                $this->clearErrors();
            }

            return $dataProvider;
        }
        $this->isValid = true;

        $from = QueryHelper::getDateFromUserTZToUtc($this->dateFrom, $this->timeZone)->format('Y-m-d H:i');
        $to = QueryHelper::getDateFromUserTZToUtc($this->dateTo, $this->timeZone)->format('Y-m-d H:i');

        if (Metrics::isSalesConversionCallPriority($this->metrics)) {
            $query->addSelect('user_data1.ud_value AS sales_conversion_call_priority');
            $query->leftJoin(['user_data1' => UserData::tableName()], "users.id = user_data1.ud_user_id AND user_data1.ud_key = '" . UserDataKey::CONVERSION_PERCENT . "'");
        }

        if (Metrics::isCallPriorityCurrent($this->metrics)) {
            $query->addSelect(UserParams::tableName() . '.up_call_user_level AS call_priority_current');
            $query->leftJoin(UserParams::tableName(), 'users.id = up_user_id');
        }

        if (Metrics::isGrossProfitCallPriority($this->metrics)) {
            $query->addSelect('user_data2.ud_value AS gross_profit_call_priority');
            $query->leftJoin(['user_data2' => UserData::tableName()], "users.id = user_data2.ud_user_id AND user_data2.ud_key = '" . UserDataKey::GROSS_PROFIT . "'");
        }

        if (Metrics::isSalesConversion($this->metrics) || Metrics::isSplitShare($this->metrics)) {
            $query->addSelect([
                'conversion_percent' =>
                    new Expression('if ((conversion_conversion_cnt is null or conversion_conversion_cnt = 0 or conversion_share is null or conversion_share = 0), 0, round((conversion_share / conversion_conversion_cnt), 2))'),
                'c_share' => 'conversion_share',
                'cc_cnt' => 'conversion_conversion_cnt'
            ]);
            $query->leftJoin([
                'conversion' => (new Query())
                    ->select([
                        'ps_user_id as conversion_user_id',
                        'sum(ps_percent) as conversion_share',
                        'if (conversion_count is null, 0, conversion_count) as conversion_conversion_cnt',
                    ])
                    ->from(ProfitSplit::tableName())
                    ->innerJoin(
                        ['l' => Lead::tableName()],
                        'l.id = ps_lead_id AND l.status = :status AND (l.l_status_dt BETWEEN :from AND :to)',
                        [':status' => Lead::STATUS_SOLD, ':from' => $from, ':to' => $to]
                    )
                    ->leftJoin([
                        'conversion_counter' => (new Query())
                            ->from(LeadUserConversion::tableName())
                            ->select([
                                'luc_user_id',
                                'count(*) as conversion_count',
                            ])
                            ->andWhere(['BETWEEN', 'luc_created_dt', $from, $to])
                            ->groupBy(['luc_user_id'])
                    ], 'conversion_counter.luc_user_id = ps_user_id')
                    ->groupBy(['conversion_user_id'])
            ], 'conversion.conversion_user_id = users.id');
            $query->addSelect([
                'split_share' =>
                    new Expression('if ((split_share_share IS NULL OR split_share_share = 0), 0, split_share_share)')
            ]);
            $query->leftJoin([
                'splitShare' => (new Query())
                    ->select([
                        'ps_user_id as split_share_user_id',
                        'SUM(ROUND((ps_percent / 100), 2)) as split_share_share',
                    ])
                    ->from(Lead::tableName())
                    ->innerJoin(ProfitSplit::tableName(), 'ps_lead_id = id')
                    ->andWhere(['status' => Lead::STATUS_SOLD])
                    ->andWhere(['BETWEEN', 'l_status_dt', $from, $to])
                    ->groupBy(['split_share_user_id'])
            ], 'splitShare.split_share_user_id = users.id');
        }

        if (Metrics::isSoldLeads($this->metrics)) {
            $query->addSelect([
                'sold_leads' =>
                    new Expression('if ((sold_leads_count IS NULL OR sold_leads_count = 0), 0, sold_leads_count)')
            ]);
            $query->leftJoin([
                'soldLeads' => (new Query())
                    ->select([
                        'ps_user_id as sold_leads_user_id',
                        'count(*) as sold_leads_count',
                    ])
                    ->from(Lead::tableName())
                    ->innerJoin(ProfitSplit::tableName(), 'ps_lead_id = id')
                    ->andWhere(['status' => Lead::STATUS_SOLD])
                    ->andWhere(['BETWEEN', 'l_status_dt', $from, $to])
                    ->groupBy(['sold_leads_user_id'])
            ], 'soldLeads.sold_leads_user_id = users.id');
        }

        if (Metrics::isQualifiedLeadsTaken($this->metrics)) {
            $query->addSelect([
                'qualified_leads_taken' =>
                    new Expression('if ((qualified_leads_taken_conversion_count IS NULL or qualified_leads_taken_conversion_count = 0), 0, qualified_leads_taken_conversion_count)')
            ]);
            $query->leftJoin([
                'qualifiedLeadsTaken' => (new Query())
                    ->from(LeadUserConversion::tableName())
                    ->select([
                        'luc_user_id as qualified_leads_taken_user_id',
                        'count(*) as qualified_leads_taken_conversion_count',
                    ])
                    ->andWhere(['BETWEEN', 'luc_created_dt', $from, $to])
                    ->groupBy(['qualified_leads_taken_user_id'])
            ], 'qualifiedLeadsTaken.qualified_leads_taken_user_id = users.id');
        }

        if (Metrics::isGrossProfit($this->metrics)) {
            $query->addSelect([
                'gross_profit' =>
                    new Expression('if ((gross_profit_gross_profit IS NULL OR gross_profit_gross_profit = 0), 0, gross_profit_gross_profit)')
            ]);
            $query->leftJoin([
                'grossProfit' => (new Query())
                    ->select([
                        'ps_user_id as gross_profit_user_id',
                        'sum(ROUND((leads.final_profit - leads.agents_processing_fee) * ps_percent/100, 2)) as gross_profit_gross_profit',
                    ])
                    ->from(['leads' => Lead::tableName()])
                    ->innerJoin(ProfitSplit::tableName(), 'ps_lead_id = leads.id')
                    ->andWhere(['status' => Lead::STATUS_SOLD])
                    ->andWhere(['IS NOT', 'final_profit', null])
                    ->andWhere(['BETWEEN', 'l_status_dt', $from, $to])
                    ->groupBy(['gross_profit_user_id'])
            ], 'grossProfit.gross_profit_user_id = users.id');
        }

        if (Metrics::isTips($this->metrics)) {
            $query->addSelect([
                'tips' =>
                    new Expression('if ((tips_tips IS NULL or tips_tips = 0), 0, tips_tips)')
            ]);
            $query->leftJoin([
                'tips' => (new Query())
                    ->select([
                        'ts_user_id as tips_user_id',
                        'sum(ROUND((leads.tips * ts_percent/100), 2)) as tips_tips',
                    ])
                    ->from(['leads' => Lead::tableName()])
                    ->innerJoin(TipsSplit::tableName(), 'ts_lead_id = leads.id')
                    ->andWhere(['status' => Lead::STATUS_SOLD])
                    ->andWhere(['IS NOT', 'tips', null])
                    ->andWhere(['BETWEEN', 'l_status_dt', $from, $to])
                    ->groupBy(['tips_user_id'])
            ], 'tips.tips_user_id = users.id');
        }

        if (Metrics::isLeadsCreated($this->metrics)) {
            $query->addSelect([
                'leads_created' =>
                    new Expression('if ((leads_created_cnt IS NULL OR leads_created_cnt = 0), 0, leads_created_cnt)')
            ]);
            $query->leftJoin([
                'leads_created' => (new Query())
                    ->select([
                        'lf.employee_id as leads_created_user_id',
                        'count(*) as leads_created_cnt'
                    ])
                    ->from(['lf' => LeadFlow::tableName()])
                    ->innerJoin(['leads' => Lead::tableName()], 'leads.id = lf.lead_id AND leads.clone_id IS NULL')
                    ->andWhere(['IS', 'lf.lf_from_status_id', null])
                    ->andWhere(['lf.status' => Lead::STATUS_PROCESSING])
                    ->andWhere(['BETWEEN', 'lf.created', $from, $to])
                    ->groupBy(['leads_created_user_id'])
            ], 'leads_created.leads_created_user_id = users.id');
        }

        if (Metrics::isLeadsProcessed($this->metrics)) {
            $query->addSelect([
                'leads_processed' =>
                    new Expression('if ((leads_processed_cnt IS NULL OR leads_processed_cnt = 0), 0, leads_processed_cnt)')
            ]);
            $query->leftJoin([
                'leads_processed' => (new Query())
                    ->select(['leads_processed_user_id', 'count(*) as leads_processed_cnt'])
                    ->from([
                        'lp_relation' => (new Query())
                            ->select([
                                'lf.lf_owner_id as leads_processed_user_id',
                                'count(*) as leads_processed_cnt'
                            ])
                            ->from(['lf' => LeadFlow::tableName()])
                            ->andWhere(['lf.status' => Lead::STATUS_PROCESSING])
                            ->andWhere(['BETWEEN', 'lf.created', $from, $to])
                            ->groupBy(['leads_processed_user_id', 'lf.lead_id'])
                    ])
                    ->groupBy(['leads_processed_user_id'])
            ], 'leads_processed.leads_processed_user_id = users.id');
        }

        if (Metrics::isLeadsTrashed($this->metrics)) {
            $query->addSelect([
                'leads_trashed' =>
                    new Expression('if ((leads_trashed_cnt IS NULL OR leads_trashed_cnt = 0), 0, leads_trashed_cnt)')
            ]);
            $query->leftJoin([
                'leads_trashed' => (new Query())
                    ->select(['leads_trashed_user_id', 'count(*) as leads_trashed_cnt'])
                    ->from([
                        'lt_relation' => (new Query())
                            ->select([
                                'lf.employee_id as leads_trashed_user_id',
                                'count(*) as leads_trashed_cnt'
                            ])
                            ->from(['lf' => LeadFlow::tableName()])
                            ->andWhere(['lf.status' => Lead::STATUS_TRASH])
                            ->andWhere(['BETWEEN', 'lf.created', $from, $to])
                            ->groupBy(['leads_trashed_user_id', 'lf.lead_id'])
                    ])
                    ->groupBy(['leads_trashed_user_id'])
            ], 'leads_trashed.leads_trashed_user_id = users.id');
        }

        if (Metrics::isLeadsToFollowUp($this->metrics)) {
            $query->addSelect([
                'leads_follow_up' =>
                    new Expression('if ((leads_follow_up_cnt IS NULL OR leads_follow_up_cnt = 0), 0, leads_follow_up_cnt)')
            ]);
            $query->leftJoin([
                'leads_follow_up' => (new Query())
                    ->select(['leads_follow_up_user_id', 'count(*) as leads_follow_up_cnt'])
                    ->from([
                        'lf_relation' => (new Query())
                            ->select([
                                'lf.employee_id as leads_follow_up_user_id',
                                'count(*) as leads_follow_up_cnt'
                            ])
                            ->from(['lf' => LeadFlow::tableName()])
                            ->andWhere(['lf.status' => Lead::STATUS_FOLLOW_UP])
                            ->andWhere(['BETWEEN', 'lf.created', $from, $to])
                            ->groupBy(['leads_follow_up_user_id', 'lf.lead_id'])
                    ])
                    ->groupBy(['leads_follow_up_user_id'])
            ], 'leads_follow_up.leads_follow_up_user_id = users.id');
        }

        if (Metrics::isLeadsCloned($this->metrics)) {
            $query->addSelect([
                'leads_cloned' =>
                    new Expression('if ((leads_cloned_cnt IS NULL OR leads_cloned_cnt = 0), 0, leads_cloned_cnt)')
            ]);
            $query->leftJoin([
                'leads_cloned' => (new Query())
                    ->select([
                        'lf.employee_id as leads_cloned_user_id',
                        'count(*) as leads_cloned_cnt'
                    ])
                    ->from(['lf' => LeadFlow::tableName()])
                    ->innerJoin(['leads' => Lead::tableName()], 'leads.id = lf.lead_id AND leads.clone_id IS NOT NULL')
                    ->andWhere(['IS', 'lf.lf_from_status_id', null])
                    ->andWhere(['lf.status' => Lead::STATUS_PROCESSING])
                    ->andWhere(['BETWEEN', 'lf.created', $from, $to])
                    ->groupBy(['leads_cloned_user_id'])
            ], 'leads_cloned.leads_cloned_user_id = users.id');
        }

        $query->andWhere(['users.status' => Employee::STATUS_ACTIVE]);

        if ($this->departments || $this->access->departmentsLimitedAccess) {
            $departments = $this->departments ?: array_keys($this->access->departments);
            $query->addSelect([
                'departmentAvailable' =>
                    (new Query())
                        ->select(['count(*)'])
                        ->from(UserDepartment::tableName())
                        ->andWhere(['ud_dep_id' => $departments])
                        ->andWhere('ud_user_id = users.id')
            ]);
            $query->andHaving(['>', 'departmentAvailable', 0]);
        }

        if ($this->groups || $this->access->groupsLimitedAccess) {
            $groups = $this->groups ?: array_keys($this->access->groups);
            $query->addSelect([
                'groupAvailable' =>
                    (new Query())
                        ->select(['count(*)'])
                        ->from(UserGroupAssign::tableName())
                        ->andWhere(['ugs_group_id' => $groups])
                        ->andWhere('ugs_user_id = users.id')
            ]);
            $query->andHaving(['>', 'groupAvailable', 0]);
        }

        if ($this->roles) {
            $query->addSelect([
                'roleAvailable' =>
                    (new Query())
                        ->select(['count(*)'])
                        ->from(['auth' => '{{%auth_assignment}}'])
                        ->andWhere(['auth.item_name' => $this->roles])
                        ->andWhere('auth.user_id = users.id')
            ]);
            $query->andHaving(['>', 'roleAvailable', 0]);
        }

        if ($this->user || $this->access->usersLimitedAccess) {
            $users = $this->user ?: array_keys($this->access->users);
            $query->andWhere(['users.id' => $users]);
        }

        if ($this->project) {
            $query->innerJoin([
                'project_employee' => ProjectEmployeeAccess::find()
                    ->select(['employee_id'])
                    ->where(['project_id' => $this->project])
                    ->groupBy(['employee_id'])
            ], 'users.id = project_employee.employee_id');
        }

        if ($this->isGroupByUserGroup()) {
            $groupQuery = (new Query())
                ->select([
                    'ug_name as group_name',
                ]);
            self::addSelectGroupMetrics($groupQuery, $this->metrics);
            $groupQuery
                ->from(['defaultQuery' => $query])
                ->leftJoin(UserGroupAssign::tableName(), 'ugs_user_id = id')
                ->leftJoin(UserGroup::tableName(), 'ug_id = ugs_group_id')
                ->groupBy(['ugs_group_id']);
            if ($this->groups || $this->access->groupsLimitedAccess) {
                $groups = $this->groups ?: array_keys($this->access->groups);
                $groupQuery->andWhere(['ugs_group_id' => $groups]);
            }
            $dataProvider->query = $groupQuery;
            $dataProvider->sort->defaultOrder = ['group_name' => SORT_ASC];
        } elseif ($this->isGroupByUserRole()) {
            $groupQuery = (new Query())
                ->select([
                    'role_name' => new Expression('if ((groupAuthItem.description IS NOT NULL), groupAuthItem.description, groupAuth.item_name)'),
                ]);
            self::addSelectGroupMetrics($groupQuery, $this->metrics);
            $groupQuery
                ->from(['defaultQuery' => $query])
                ->leftJoin(['groupAuth' => '{{%auth_assignment}}'], 'groupAuth.user_id = id')
                ->leftJoin(['groupAuthItem' => '{{%auth_item}}'], 'groupAuthItem.name = groupAuth.item_name')
                ->groupBy(['role_name']);
            $groupQuery->andFilterWhere(['groupAuth.item_name' => $this->roles]);
            $dataProvider->query = $groupQuery;
            $dataProvider->sort->defaultOrder = ['role_name' => SORT_ASC];
        }

        $this->calculateSummaryStats($dataProvider->query);

//        VarDumper::dump($query->createCommand()->getRawSql());die;

        return $dataProvider;
    }

    public function calculateSummaryStats(Query $query): void
    {
        $data = [];
        $results = $query->all();
        if (Metrics::isSalesConversionCallPriority($this->metrics)) {
            $data['sales_conversion_call_priority'] = [
                'Name' => 'Sales Conversion Call Priority',
                'total' => $this->getSumColumn(
                    $results,
                    'sales_conversion_call_priority'
                ),
                'average' => $this->getAvgValueColumn(
                    $results,
                    'sales_conversion_call_priority'
                ),
            ];
        }
        if (Metrics::isCallPriorityCurrent($this->metrics)) {
            $data['call_priority_current'] = [
                'Name' => 'Call Priority Current',
                'total' => $this->getSumColumn(
                    $results,
                    'call_priority_current'
                ),
                'average' => $this->getAvgValueColumn(
                    $results,
                    'call_priority_current'
                ),
            ];
        }
        if (Metrics::isGrossProfitCallPriority($this->metrics)) {
            $data['gross_profit_call_priority'] = [
                'Name' => 'Gross Profit Call Priority',
                'total' => $this->getSumColumn(
                    $results,
                    'gross_profit_call_priority'
                ),
                'average' => $this->getAvgValueColumn(
                    $results,
                    'gross_profit_call_priority'
                ),
            ];
        }
        if (Metrics::isSalesConversion($this->metrics)) {
            $data['conversion_percent'] = [
                'Name' => 'Conversion Percent',
                'average' => $this->getConversionPercent($results),
                'total' => null
            ];
        }
        if (Metrics::isSoldLeads($this->metrics)) {
            $data['sold_leads'] = [
                'Name' => 'Sold Leads',
                'average' => $this->getAvgValueColumn(
                    $results,
                    'sold_leads'
                ),
                'total' => $this->getSumColumn(
                    $results,
                    'sold_leads'
                ),
            ];
        }
        if (Metrics::isSplitShare($this->metrics)) {
            $data['split_share'] = [
                'Name' => 'Split Share',
                'total' => $this->getSumColumn(
                    $results,
                    'split_share'
                ),
                'average' => $this->getAvgValueColumn(
                    $results,
                    'split_share'
                ),
            ];
        }
        if (Metrics::isQualifiedLeadsTaken($this->metrics)) {
            $data['qualified_leads_taken'] = [
                'Name' => 'Qualified Leads Taken',
                'total' => $this->getSumColumn(
                    $results,
                    'qualified_leads_taken'
                ),
                'average' => $this->getAvgValueColumn(
                    $results,
                    'qualified_leads_taken'
                ),
            ];
        }
        if (Metrics::isGrossProfit($this->metrics)) {
            $data['gross_profit'] = [
                'Name' => 'Gross Profit',
                'total' => \Yii::$app->formatter->asNumCurrency($this->getSumColumn(
                    $results,
                    'gross_profit'
                )),
                'average' => \Yii::$app->formatter->asNumCurrency($this->getAvgValueColumn(
                    $results,
                    'gross_profit'
                )),
            ];
        }
        if (Metrics::isTips($this->metrics)) {
            $data['tips'] = [
                'Name' => 'Tips',
                'total' => \Yii::$app->formatter->asNumCurrency($this->getSumColumn(
                    $results,
                    'tips'
                )),
                'average' => \Yii::$app->formatter->asNumCurrency($this->getAvgValueColumn(
                    $results,
                    'tips'
                )),
            ];
        }
        if (Metrics::isLeadsCreated($this->metrics)) {
            $data['leads_created'] = [
                'Name' => 'Leads Created',
                'total' => $this->getSumColumn(
                    $results,
                    'leads_created'
                ),
                'average' => $this->getAvgValueColumn(
                    $results,
                    'leads_created'
                ),
            ];
        }
        if (Metrics::isLeadsProcessed($this->metrics)) {
            $data['leads_processed'] = [
                'Name' => 'Leads Processed',
                'total' => $this->getSumColumn(
                    $results,
                    'leads_processed'
                ),
                'average' => $this->getAvgValueColumn(
                    $results,
                    'leads_processed'
                ),
            ];
        }
        if (Metrics::isLeadsTrashed($this->metrics)) {
            $data['leads_trashed'] = [
                'Name' => 'Leads Trashed',
                'total' => $this->getSumColumn(
                    $results,
                    'leads_trashed'
                ),
                'average' => $this->getAvgValueColumn(
                    $results,
                    'leads_trashed'
                ),
            ];
        }
        if (Metrics::isLeadsToFollowUp($this->metrics)) {
            $data['leads_follow_up'] = [
                'Name' => 'Leads Follow Up',
                'total' => $this->getSumColumn(
                    $results,
                    'leads_follow_up'
                ),
                'average' => $this->getAvgValueColumn(
                    $results,
                    'leads_follow_up'
                ),
            ];
        }
        if (Metrics::isLeadsCloned($this->metrics)) {
            $data['leads_cloned'] = [
                'Name' => 'Leads Cloned',
                'total' => $this->getSumColumn(
                    $results,
                    'leads_cloned'
                ),
                'average' => $this->getAvgValueColumn(
                    $results,
                    'leads_cloned'
                ),
            ];
        }
        $this->summaryStats = $data;
    }

    public function getSummaryStats(): array
    {
        return $this->summaryStats;
    }

    private function getSumColumn(array $array, string $key)
    {
        return array_sum(array_column($array, $key));
    }

    private function getAvgValueColumn(array $array, string $key, int $precision = 2)
    {
        $count = count($array);
        if ($count) {
            return round($this->getSumColumn($array, $key) / $count, $precision);
        }
        return 0;
    }

    private function getConversionPercent(array $results): float
    {
        $sumSplitShare = $this->getSumColumn(
            $results,
            'split_share'
        );
        $sumQualifiedLeadsTaken = $this->getSumColumn(
            $results,
            'qualified_leads_taken'
        );
        if ($sumQualifiedLeadsTaken) {
            return round(($sumSplitShare / $sumQualifiedLeadsTaken) * 100, 2);
        }
        return 0;
    }

    public static function addSelectGroupMetrics(Query $query, array $metrics): void
    {
        if (Metrics::isLeadsCreated($metrics)) {
            $query->addSelect(['sum(leads_created) as leads_created']);
        }
        if (Metrics::isSalesConversion($metrics) || Metrics::isSplitShare($metrics)) {
            //$query->addSelect(['round(sum(conversion_percent)/(count(*)), 2) as conversion_percent']);
            $query->addSelect(['if ((round(sum(c_share) / sum(cc_cnt), 2) IS NULL OR round(sum(c_share) / sum(cc_cnt), 2) = 0), 0, round(sum(c_share) / sum(cc_cnt), 2)) as conversion_percent']);
            $query->addSelect(['round(sum(split_share)/(count(*)), 2) as split_share']);
        }
        if (Metrics::isSoldLeads($metrics)) {
            $query->addSelect(['sum(sold_leads) as sold_leads']);
        }
        if (Metrics::isQualifiedLeadsTaken($metrics)) {
            $query->addSelect(['sum(qualified_leads_taken) as qualified_leads_taken']);
        }
        if (Metrics::isGrossProfit($metrics)) {
            $query->addSelect(['sum(gross_profit) as gross_profit']);
        }
        if (Metrics::isTips($metrics)) {
            $query->addSelect(['sum(tips) as tips']);
        }
        if (Metrics::isLeadsProcessed($metrics)) {
            $query->addSelect(['sum(leads_processed) as leads_processed']);
        }
        if (Metrics::isLeadsTrashed($metrics)) {
            $query->addSelect(['sum(leads_trashed) as leads_trashed']);
        }
        if (Metrics::isLeadsToFollowUp($metrics)) {
            $query->addSelect(['sum(leads_follow_up) as leads_follow_up']);
        }
        if (Metrics::isLeadsCloned($metrics)) {
            $query->addSelect(['sum(leads_cloned) as leads_cloned']);
        }
    }

    public function getDepartmentList(): array
    {
        return $this->access->departments;
    }

    public function getMetricsList(): array
    {
        return Metrics::LIST;
    }

    public function getRolesList(): array
    {
        return $this->access->roles;
    }

    public function getGroupList(): array
    {
        return $this->access->groups;
    }

    public function getUsersList(): array
    {
        return $this->access->users;
    }

    public function isGroupByUserName(): bool
    {
        return $this->groupBy === self::GROUP_BY_USER_NAME;
    }

    public function isGroupByUserGroup(): bool
    {
        return $this->groupBy === self::GROUP_BY_USER_GROUP;
    }

    public function isGroupByUserRole(): bool
    {
        return $this->groupBy === self::GROUP_BY_USER_ROLE;
    }

    public function getGroupByList(): array
    {
        return self::GROUP_BY_LIST;
    }

    public function validateRange($attribute, $params)
    {
        $range = explode(' - ', $this->$attribute);
        if (count($range) !== 2) {
            $this->addError($attribute, 'Range From date or To date is incorrect');
            return;
        }

        $dateTimeValidator = new DateValidator([
            'type' => DateValidator::TYPE_DATETIME,
            'format' => 'php:Y-m-d H:i'
        ]);

        $dateTimeValidator->validate($range[0], $errors);
        if ($errors) {
            $this->addError($attribute, 'Range From date is incorrect');
            return;
        }

        $dateTimeValidator->validate($range[1], $errors);
        if ($errors) {
            $this->addError($attribute, 'Range To date is incorrect');
            return;
        }

        $from = new \DateTimeImmutable($range[0]);
        $to = new \DateTimeImmutable($range[1]);
        if ($from > $to) {
            $this->addError($attribute, 'Range From date more than To date');
            return;
        }
        if ($from == $to) {
            $this->addError($attribute, 'Range From date and To date is equal');
            return;
        }

        $paramsFrom = new \DateTimeImmutable($params['minStartFrom']);
        if ($from < $paramsFrom) {
            $this->addError($attribute, 'Range From date must be more or equal than ' . $paramsFrom->format('Y-m-d H:i'));
            return;
        }

        $paramsTo = new \DateTimeImmutable($params['maxEndTo']);
        if ($to > $paramsTo) {
            $this->addError($attribute, 'Range To date must be less or equal than ' . $paramsTo->format('Y-m-d H:i'));
            return;
        }

        $this->dateFrom = $from->format('Y-m-d H:i');
        $this->dateTo = $to->format('Y-m-d H:i');
    }

    public function getFilters(): array
    {
        return [
            $this->formName() => [
                'groupBy' => $this->groupBy,
                'timeZone' => $this->timeZone,
                'dateRange' => $this->dateRange,
                'departments' => $this->departments,
                'roles' => $this->roles,
                'groups' => $this->groups,
                'user' => $this->user,
                'metrics' => $this->metrics,
            ]
        ];
    }
}
