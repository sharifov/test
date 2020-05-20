<?php

namespace sales\entities\call;

use common\models\Employee;
use sales\model\callLog\entity\callLog\CallLog;
use sales\model\callLog\entity\callLog\search\CallLogSearch;
use sales\model\callLog\entity\callLog\CallLogType;
use sales\model\callLog\entity\callLog\CallLogStatus;
use sales\model\callLog\entity\callLog\CallLogCategory;
use common\models\UserGroupAssign;
use kartik\daterange\DateRangeBehavior;
use Yii;
use yii\data\SqlDataProvider;
use yii\db\ActiveRecord;
use DateTime;

/**
 * Class CallGraphsSearch
 * @package common\models\search
 *
 * @property string $createTimeRange
 * @property int $createTimeStart
 * @property int $createTimeEnd
 *
 * @property int $callDepId
 * @property int $recordingDurationFrom
 * @property int $recordingDurationTo
 * @property int $betweenHoursFrom
 * @property int $betweenHoursTo
 * @property int $callGraphGroupBy
 * @property array $totalChartColumns
 * @property int $chartTotalCallsVaxis
 * @property array $projectIds
 * @property array $dep_ids
 * @property array $userGroupIds
 * @property mixed $notAnsweredOutgoingCallsQuery
 * @property mixed $completeOutgoingCallsQuery
 * @property mixed $notAnsweredIncomingCallsQuery
 * @property string|mixed $defaultDateFormat
 * @property mixed $completeIncomingCallsQuery
 * @property string $partitionsByYears
 * @property \yii\data\SqlDataProvider $totalCalls
 * @property string $timeZone
 */
class CallGraphsSearch extends CallLogSearch
{
    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;

    public $recordingDurationFrom;
    public $recordingDurationTo;
    public $betweenHoursFrom;
    public $betweenHoursTo;
    public $callGraphGroupBy;
    public $totalChartColumns;
    public $chartTotalCallsVaxis;
    public $projectIds = [];
    public $dep_ids = [];
    public $userGroupIds = [];
    public $timeZone;

    private const DAYS_DIFF_MAX_RANGE = 7;
    private const MONTH_DIFF_MAX_RANGE = 2;
    private const Year_DIFF_MAX_RANGE = 1;

    public const DATE_FORMAT_DAYS = 0;
    public const DATE_FORMAT_HOURS = 4;
    public const DATE_FORMAT_WEEKS = 2;
    public const DATE_FORMAT_MONTH = 3;
    public const DATE_FORMAT_HOURS_DAYS = 1;
    public const DATE_FORMAT_WEEKDAYS = 5;

    public const CREATE_TIME_START_DEFAULT = '-6 days';

    public const DATE_FORMAT_TEXT = [
        self::DATE_FORMAT_DAYS => 'Day',
        self::DATE_FORMAT_HOURS => 'Hour',
        self::DATE_FORMAT_WEEKS => 'Week',
        self::DATE_FORMAT_MONTH => 'Month',
        self::DATE_FORMAT_HOURS_DAYS => 'Hour of the Day',
        self::DATE_FORMAT_WEEKDAYS => 'Day of the Week',

    ];

    public const DATE_FORMAT_LIST_ID = [
        self::DATE_FORMAT_HOURS,
        self::DATE_FORMAT_DAYS,
        self::DATE_FORMAT_WEEKS,
        self::DATE_FORMAT_MONTH,
        self::DATE_FORMAT_HOURS_DAYS,
        self::DATE_FORMAT_WEEKDAYS
    ];

    public const DATE_FORMAT_LIST = [
        self::DATE_FORMAT_HOURS_DAYS => '%H:00',
        self::DATE_FORMAT_HOURS => '%Y-%m-%d %H:00',
        self::DATE_FORMAT_DAYS => '%Y-%m-%d',
        self::DATE_FORMAT_WEEKS => '%v',
        self::DATE_FORMAT_MONTH => '%Y-%M',
        self::DATE_FORMAT_WEEKDAYS => '%W',
    ];

    private const DATE_FORMAT_LIST_COUNT = [
        self::DATE_FORMAT_HOURS => '%H:00',
        self::DATE_FORMAT_HOURS_DAYS => '%Y-%m-%d',
        self::DATE_FORMAT_DAYS => '%Y-%m-%d',
        self::DATE_FORMAT_MONTH => '%Y-%M',
        self::DATE_FORMAT_WEEKDAYS => '%Y-%m-%d'
    ];

    public const CHART_TOTAL_CALLS_INCOMING = 1;
    public const CHART_TOTAL_CALLS_OUTGOING = 2;
    public const CHART_TOTAL_CALLS_TOTAL = 3;

    private const CHART_TOTAL_CALLS_TEXT = [
        self::CHART_TOTAL_CALLS_INCOMING => 'Incoming',
        self::CHART_TOTAL_CALLS_OUTGOING => 'Outgoing',
        self::CHART_TOTAL_CALLS_TOTAL => 'Total'
    ];

    private const CHART_TOTAL_CALLS_LIST = [
        self::CHART_TOTAL_CALLS_INCOMING,
        self::CHART_TOTAL_CALLS_OUTGOING,
        self::CHART_TOTAL_CALLS_TOTAL,
    ];

    public const CHART_TOTAL_CALLS_VAXIS_CALLS = 1;
    public const CHART_TOTAL_CALLS_VAXIS_CALLS_AVG = 2;
    public const CHART_TOTAL_CALLS_VAXIS_REC_DURATION = 3;
    public const CHART_TOTAL_CALLS_VAXIS_REC_DURATION_AVG = 4;

    private const CHART_TOTAL_CALLS_VAXIS_TEXT = [
        self::CHART_TOTAL_CALLS_VAXIS_CALLS => 'Number of Calls',
        self::CHART_TOTAL_CALLS_VAXIS_CALLS_AVG => 'Number of Calls AVG',
        self::CHART_TOTAL_CALLS_VAXIS_REC_DURATION => 'Call Duration',
        self::CHART_TOTAL_CALLS_VAXIS_REC_DURATION_AVG => 'Call Duration AVG',
    ];

    private const CHART_TOTAL_CALLS_VAXIS_LIST = [
        self::CHART_TOTAL_CALLS_VAXIS_CALLS,
        self::CHART_TOTAL_CALLS_VAXIS_CALLS_AVG,
        self::CHART_TOTAL_CALLS_VAXIS_REC_DURATION,
        self::CHART_TOTAL_CALLS_VAXIS_REC_DURATION_AVG,
    ];

    public const GRAPH_ALL_CALLS = 1;

    public const GRAPH_LIST = [
        self::GRAPH_ALL_CALLS
    ];

    /**
     * CallGraphsSearch constructor.
     * @param array $config
     * @throws \yii\base\InvalidConfigException
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->createTimeRange = date('Y-m-d 00:00:00', strtotime(self::CREATE_TIME_START_DEFAULT)) . ' - ' . date('Y-m-d H:i:s');
        $this->betweenHoursFrom = 0;
        $this->betweenHoursTo = 24;
        $this->recordingDurationFrom = 30;
        $this->timeZone = $this->timeZone ?? Yii::$app->user->identity->timezone;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['cl_id', 'cl_type_id', 'cl_user_id', 'cl_project_id', 'cl_duration', 'cl_client_id', 'cl_status_id', 'recordingDurationFrom', 'recordingDurationTo', 'betweenHoursFrom', 'betweenHoursTo', 'callGraphGroupBy', 'chartTotalCallsVaxis'], 'integer'],
            [['cl_call_created_dt', 'statuses', 'limit', 'projectId', 'statusId', 'callTypeId'], 'safe'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['dep_ids', 'totalChartColumns', 'projectIds', 'userGroupIds'], 'each', 'rule' => ['integer']],
            ['recordingDurationTo', 'compare', 'compareAttribute' => 'recordingDurationFrom', 'operator' => '>='],
            ['betweenHoursTo', 'compare', 'compareAttribute' => 'betweenHoursFrom', 'operator' => '>='],
            [['betweenHoursFrom', 'betweenHoursTo'], 'number', 'min' => 0, 'max' => 24],
            ['callGraphGroupBy', 'in', 'range' => self::DATE_FORMAT_LIST_ID],
            ['timeZone', 'string'],
            ['timeZone', 'required'],
            ['callGraphGroupBy', 'filter', 'filter' => 'intval']
        ];
    }

    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_AFTER_VALIDATE => 'afterValidate'
        ];
    }

    /**
     * @return bool|void
     * @throws \Exception
     */
    public function afterValidate()
    {
        parent::afterValidate();

        $dateStart = new DateTime(date('Y-m-d H:i:s', $this->createTimeStart));
        $dateEnd = new DateTime(date('Y-m-d H:i:s', $this->createTimeEnd));

        if (((int)$this->callGraphGroupBy === self::DATE_FORMAT_HOURS || (int)$this->callGraphGroupBy === self::DATE_FORMAT_HOURS_DAYS) && $this->createTimeStart && $this->createTimeEnd) {

            $daysDiff = $dateEnd->diff($dateStart)->format('%a');

            if ($daysDiff >= self::DAYS_DIFF_MAX_RANGE) {
                $this->addError('callGraphGroupBy', 'The difference between two dates cannot be more than ' . self::DAYS_DIFF_MAX_RANGE . ' days when grouped by ' . self::DATE_FORMAT_TEXT[$this->callGraphGroupBy]);
                return false;
            }
        } else if ((int)$this->callGraphGroupBy === self::DATE_FORMAT_DAYS && $this->createTimeStart && $this->createTimeEnd) {
            $monthsDiff = $dateEnd->diff($dateStart)->format('%m');

            if ($monthsDiff >= self::MONTH_DIFF_MAX_RANGE) {
                $this->addError('callGraphGroupBy', 'The difference between two dates cannot be more than ' . self::MONTH_DIFF_MAX_RANGE . ' month when grouped by Days');
                return false;
            }
        } else if ((int)$this->callGraphGroupBy === self::DATE_FORMAT_WEEKS && $this->createTimeStart && $this->createTimeEnd) {
            $yearsDiff = $dateEnd->diff($dateStart)->format('%y');

            if ($yearsDiff >= self::Year_DIFF_MAX_RANGE) {
                $this->addError('callGraphGroupBy', 'The difference between two dates cannot be more than ' . self::Year_DIFF_MAX_RANGE . ' year when grouped by Weeks');
                return false;
            }
        }

        if (empty($this->totalChartColumns)) {
            $this->totalChartColumns = self::getChartTotalCallList();
        }
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => DateRangeBehavior::class,
                'attribute' => 'createTimeRange',
                'dateStartAttribute' => 'createTimeStart',
                'dateEndAttribute' => 'createTimeEnd',
            ]
        ];
    }

    public function getCallLogStats()
    {
        $parentQuery = self::find()->select([
            //'hour(cl_call_created_dt) as `group`',
            'TIME_FORMAT(cl_call_created_dt, \'%H:%00\') as `group`',
            'CASE cl_type_id WHEN 1 THEN \'out\' WHEN 2 THEN \'in\' END `callType`',
            'COUNT(*) as `totalCalls`',
            'count(*)/count(distinct(DATE_FORMAT(cl_call_created_dt, \'%Y-%m-%d\'))) `avgCallsPerGroup`',
            'sum(cl_duration)as `totalCallsDuration`',
            'avg(cl_duration) as `avgCallDuration`'
        ]);
        $parentQuery->where(['between', 'cl_call_created_dt', '2020-03-01 00:00:00', '2020-03-01 00:59:59']);
        $parentQuery->andWhere(['>', 'cl_duration', '30']);
        $parentQuery->andWhere(['cl_status_id' => [CallLogStatus::COMPLETE, CallLogStatus::BUSY, CallLogStatus::NOT_ANSWERED]]);
        $parentQuery->andWhere(['OR',
            ['cl_category_id' => null],
            ['<>', 'cl_category_id', CallLogCategory::TRANSFER_CALL]
        ]);

        $parentQuery->groupBy([new \yii\db\Expression('1,2')]);

        $childQuery = self::find()->select([
            //'hour(cl_call_created_dt) as `group`',
            'TIME_FORMAT(cl_call_created_dt, \'%H:%00\') AS group',
            '"total" AS `callType`',
            'count(*) as `totalCalls`',
            'count(*)/count(distinct(DATE_FORMAT(cl_call_created_dt, \'%Y-%m-%d\'))) `avgCallsPerGroup`',
            'sum(cl_duration)as `totalCallsDuration`',
            'avg(cl_duration) as `avgCallDuration`'
        ]);
        $childQuery->where(['between', 'cl_call_created_dt', '2020-03-01 00:00:00', '2020-03-01 00:59:59']);
        $childQuery->andWhere(['>', 'cl_duration', '30']);
        $childQuery->andWhere(['cl_status_id' => [CallLogStatus::COMPLETE, CallLogStatus::BUSY, CallLogStatus::NOT_ANSWERED]]);
        $childQuery->andWhere(['OR',
            ['cl_category_id' => null],
            ['<>', 'cl_category_id', CallLogCategory::TRANSFER_CALL]
        ]);

        $childQuery->groupBy([new \yii\db\Expression('1')]);

        $parentQuery->union($childQuery);


        /*$cmdP = $parentQuery->createCommand();
        $cmdC = $childQuery->createCommand();
        //var_dump($cmdP->createCommand()->rawSql); die();
        var_dump($cmdP->queryAll()); die();*/

        return new SqlDataProvider(['sql' => $parentQuery->createCommand()->rawSql, 'pagination' => false]);
    }

    /**
     * @return SqlDataProvider
     */
    public function getTotalCalls(): SqlDataProvider
    {
        $dateFormat = $this->getDateFormat($this->callGraphGroupBy) ?? $this->getDefaultDateFormat();

        $query = self::find()->select([
            'sum(incoming) as incoming',
            'sum(outgoing) as outgoing',
            'sum(incoming + outgoing) as total_calls',
            'sum(incoming_duration_sum) as in_rec_duration',
            'sum(outgoing_duration_sum) as out_rec_duration',
            'sum(tbl.incoming_duration_sum + tbl.outgoing_duration_sum) as total_rec_duration',
            'coalesce(sum(incoming_duration_sum) / sum(incoming), 0) as incoming_duration_avg',
            'coalesce(sum(outgoing_duration_sum) / sum(outgoing), 0) as outgoing_duration_avg',
            'coalesce(sum(tbl.incoming_duration_sum + tbl.outgoing_duration_sum) / sum(incoming + outgoing),0) as total_rec_duration_avg',
            'sum(case when incoming_duration_sum > 0 then 1 else 0 end) as \'inc_dur_count\'',
            'sum(case when outgoing_duration_sum > 0 then 1 else 0 end) as \'out_dur_count\''
        ]);

        if ((int)$this->callGraphGroupBy === self::DATE_FORMAT_WEEKS) {
            $query->addSelect(["concat(str_to_date(date_format(created, '%Y %v Monday'), '%x %v %W'), ' - ', str_to_date(date_format(created, '%Y %v Sunday'), '%x %v %W')) as created_formatted"]);
        } else {
            $query->addSelect(["date_format(`created`, '$dateFormat') as created_formatted"]);
        }

        if ($this->createTimeRange) {
            $this->createTimeStart = date('Y-m-d H:i:00', $this->createTimeStart);
            $this->createTimeEnd = date('Y-m-d H:i:59', $this->createTimeEnd);
        } else {
            $this->createTimeStart = date('Y-m-d 00:00:00', strtotime(self::CREATE_TIME_START_DEFAULT));
            $this->createTimeEnd = date('Y-m-d H:i:s');
            $this->createTimeRange = $this->createTimeStart . ' - ' . $this->createTimeEnd;
        }

        if (!$this->cl_duration) {
            $this->cl_duration = 2;
        }

        $incomingComplete = $this->getCompleteIncomingCallsQuery();
        $incomingNotAnswered = $this->getNotAnsweredIncomingCallsQuery();
        $outgoingComplete = $this->getCompleteOutgoingCallsQuery();
        $outgoingNotAnswered = $this->getNotAnsweredOutgoingCallsQuery();

        $query->from(['tbl' => $incomingComplete->union($incomingNotAnswered)
            ->union($outgoingComplete)
            ->union($outgoingNotAnswered)
        ])->groupBy(['created_formatted']);

        if ($this->callGraphGroupBy === self::DATE_FORMAT_HOURS_DAYS) {
            $order = [
                'created_formatted' => SORT_ASC,
            ];
        } else {
            $order = [
                'created' => SORT_ASC,
            ];
        }
        $query->orderBy($order);

        return new SqlDataProvider(['sql' => $query->createCommand()->rawSql, 'pagination' => false]);
    }

    private function getCompleteIncomingCallsQuery()
    {
        $query = CallLog::find()
            ->select([
                'count(cl_id) as incoming',
                'coalesce(0) as outgoing',
                'coalesce(sum(cl_duration), 0) as incoming_duration_sum',
                'coalesce(0) as outgoing_duration_sum'
            ])
            ->andWhere(['cl_type_id' => CallLogType::IN])
            ->andWhere(['cl_status_id' => CallLogStatus::COMPLETE])
            ->andWhere(['<>','cl_category_id', CallLogCategory::TRANSFER_CALL]);

        if ($this->recordingDurationFrom) {
            $query->andWhere(['>=', 'cl_duration', $this->recordingDurationFrom]);
        }

        if ($this->recordingDurationTo) {
            $query->andWhere(['<=', 'cl_duration', $this->recordingDurationTo]);
        }

        return $this->applySearchQuery($query);
    }

    private function getNotAnsweredIncomingCallsQuery()
    {
        $query = CallLog::find()
            ->select([
                'count(cl_id) as incoming',
                'coalesce(0) as outgoing',
                'coalesce(sum(cl_duration), 0) as incoming_duration_sum',
                'coalesce(0) as outgoing_duration_sum'
            ])
            ->andWhere(['cl_type_id' => CallLogType::IN])
            ->andWhere(['cl_status_id' => [CallLogStatus::BUSY, CallLogStatus::NOT_ANSWERED]])
            ->andWhere(['<>','cl_category_id', CallLogCategory::TRANSFER_CALL]);

        if ($this->recordingDurationFrom) {
            $query->andWhere(['>=', 'cl_duration', $this->recordingDurationFrom]);
        }

        if ($this->recordingDurationTo) {
            $query->andWhere(['<=', 'cl_duration', $this->recordingDurationTo]);
        }

        return $this->applySearchQuery($query);
    }

    private function getCompleteOutgoingCallsQuery()
    {
        $query = CallLog::find()
            ->select([
                'coalesce(0) as incoming',
                'count(cl_id) as outgoing',
                'coalesce(0) as incoming_duration_sum',
                'coalesce(sum(cl_duration), 0) as outgoing_duration_sum'
            ])
            ->andWhere(['cl_type_id' => CallLogType::OUT])
            ->andWhere(['cl_status_id' => CallLogStatus::COMPLETE])
            ->andWhere(['<>','cl_category_id', CallLogCategory::TRANSFER_CALL]);

        if ($this->recordingDurationFrom) {
            $query->andWhere(['>=', 'cl_duration', $this->recordingDurationFrom]);
        }

        if ($this->recordingDurationTo) {
            $query->andWhere(['<=', 'cl_duration', $this->recordingDurationTo]);
        }

        return $this->applySearchQuery($query);
    }

    private function getNotAnsweredOutgoingCallsQuery()
    {
        $query = CallLog::find()
            ->select([
                'coalesce(0) as incoming',
                'count(cl_id) as outgoing',
                'coalesce(0) as incoming_duration_sum',
                'coalesce(sum(cl_duration), 0) as outgoing_duration_sum'
            ])
            ->andWhere(['cl_type_id' => CallLogType::OUT])
            ->andWhere(['cl_status_id' => [CallLogStatus::BUSY, CallLogStatus::NOT_ANSWERED]])
            ->andWhere(['<>','cl_category_id', CallLogCategory::TRANSFER_CALL]);

        return $this->applySearchQuery($query);
    }

    private function applySearchQuery($query)
    {
        $timeZone = Employee::getUtcOffsetDst($this->timeZone, date('Y-m-d'));

        $query->from([new \yii\db\Expression(CallLog::tableName(). ' PARTITION('. $this->getPartitionsByYears() .') ')]);

        $query->addSelect(["date_format(convert_tz(cl_call_created_dt, '+00:00', '$timeZone'), '%Y-%m-%d %H:00:00') as created"]);

        $query->andWhere(['between', "convert_tz(cl_call_created_dt, '+00:00', '".$timeZone."')", $this->createTimeStart, $this->createTimeEnd]);

        if ($this->projectIds) {
            $query->andWhere(['cl_project_id' => $this->projectIds]);
        }

        if ($this->dep_ids) {
            $query->andWhere(['cl_department_id' => $this->dep_ids]);
        }

        if ($this->cl_user_id) {
            $query->andWhere(['cl_user_id' => $this->cl_user_id]);
        } else if ($this->userGroupIds) {
            $userIdsByGroup = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id) as cl_user_id'])->where(['ugs_group_id' => $this->userGroupIds])->asArray()->all();
            if ($userIdsByGroup) {
                $query->andWhere(['in', ['cl_user_id'], $userIdsByGroup]);
            }
        }

        if ($this->betweenHoursFrom) {
            $query->andWhere(['>=', 'hour(convert_tz(cl_call_created_dt, \'+00:00\', \''.$timeZone.'\'))', $this->betweenHoursFrom]);
        }

        if ($this->betweenHoursTo) {
            $query->andWhere(['<=', 'hour(convert_tz(cl_call_created_dt, \'+00:00\', \''.$timeZone.'\'))', $this->betweenHoursTo]);
        }

        $query->groupBy('created')->orderBy('created');

        return $query;
    }

    private function getPartitionsByYears()
    {
        $yFrom = date('y', strtotime($this->createTimeStart));
        $yTo = date('y', strtotime($this->createTimeEnd));
        $partitions = 'y';
        if ($yFrom == $yTo){
            $nextYear = (int)$yFrom + 1 ;
            $partitions = 'y' . $nextYear ;
        } else {
            $nextYearFrom = (int)$yFrom + 1 ;
            $nextYearTo = (int)$yTo + 1 ;
            $partitions = 'y'. $nextYearFrom . ',' . 'y' . $nextYearTo ;
        }

        return $partitions;
    }

    /**
     * @return array
     */
    public static function getDateFormatTextList(): array
    {
        return self::DATE_FORMAT_TEXT;
    }

    /**
     * @return array
     */
    public static function getChartTotalCallList(): array
    {
        return self::CHART_TOTAL_CALLS_LIST;
    }

    /**
     * @return array
     */
    public static function getChartTotalCallTextList(): array
    {
        return self::CHART_TOTAL_CALLS_TEXT;
    }

    /**
     * @return array
     */
    public static function getChartTotalCallsVaxisList(): array
    {
        return self::CHART_TOTAL_CALLS_VAXIS_LIST;
    }

    /**
     * @return array
     */
    public static function getChartTotalCallsVaxisTextList(): array
    {
        return self::CHART_TOTAL_CALLS_VAXIS_TEXT;
    }

    public static function getChartTotalCallsVaxisText($vaxisId): string
    {
        return self::getChartTotalCallsVaxisTextList()[$vaxisId] ?? '';
    }

    /**
     * @param $dateFormatId
     * @return string|null
     */
    private function getDateFormat($dateFormatId): ?string
    {
        return self::DATE_FORMAT_LIST[$dateFormatId] ?? null;
    }

    /**
     * @return mixed
     */
    private function getDefaultDateFormat()
    {
        return self::DATE_FORMAT_LIST[self::DATE_FORMAT_DAYS];
    }

}