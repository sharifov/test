<?php

namespace sales\entities\call;

use common\models\Call;
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
 * @property-read \yii\data\SqlDataProvider $callLogStats
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

    public const CREATE_TIME_START_DEFAULT = '0 days';

    public const DATE_FORMAT_TEXT = [
        self::DATE_FORMAT_HOURS => 'Hour',
        self::DATE_FORMAT_DAYS => 'Day',
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

    public const GROUP_FORMAT_HOURS = 'H:00:00';
    public const GROUP_FORMAT_DAYS_HOURS = 'Y-m-d H:00';

    public const DATE_FORMAT_LIST = [
        self::DATE_FORMAT_HOURS_DAYS => '%H:00',
        self::DATE_FORMAT_HOURS => '%Y-%m-%d %H:00',
        self::DATE_FORMAT_DAYS => '%Y-%m-%d',
        self::DATE_FORMAT_WEEKS => '%v',
        self::DATE_FORMAT_MONTH => '%Y-%m',
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
        /*self::CHART_TOTAL_CALLS_TOTAL => 'Total'*/
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
        $this->createTimeRange = date('Y-m-d 00:00:00', strtotime(self::CREATE_TIME_START_DEFAULT)) . ' - ' . date('Y-m-d 23:59:59');
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

        /*$dateStart = new DateTime(date('Y-m-d H:i:s', $this->createTimeStart));
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
        }*/

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

    /**
     * @return SqlDataProvider
     */
    public function getCallLogStats():SqlDataProvider
    {
        if ($this->createTimeRange) {
            $this->createTimeStart = date('Y-m-d H:i:00', $this->createTimeStart);
            $this->createTimeEnd = date('Y-m-d H:i:59', $this->createTimeEnd);
        } else {
            $this->createTimeStart = date('Y-m-d 00:00:00', strtotime(self::CREATE_TIME_START_DEFAULT));
            $this->createTimeEnd = date('Y-m-d H:i:s');
            $this->createTimeRange = $this->createTimeStart . ' - ' . $this->createTimeEnd;
        }

        $timeZone = Employee::getUtcOffsetDst($this->timeZone, $this->createTimeStart);

        $parentQuery = self::find()->select([
            ''. $this->setGroupingParam() .' AS `group`',
            'CASE cl_type_id WHEN 1 THEN \'out\' WHEN 2 THEN \'in\' END `callType`',
            'COUNT(*) as `totalCalls`',
            'count(*)/count(distinct(DATE_FORMAT(cl_call_created_dt, \'%Y-%m-%d\'))) `avgCallsPerGroup`',
            'sum(cl_duration)as `totalCallsDuration`',
            'avg(cl_duration) as `avgCallDuration`'
        ]);
        $parentQuery->from([new \yii\db\Expression(CallLog::tableName(). ' PARTITION('. $this->getPartitionsByYears() .') ')]);

        $parentQuery->andWhere([
            'between',
            'cl_call_created_dt',
            Employee::convertTimeFromUserDtToUTC(strtotime($this->createTimeStart)),
            Employee::convertTimeFromUserDtToUTC(strtotime($this->createTimeEnd))
        ]);

        $parentQuery->andWhere(['cl_status_id' => [CallLogStatus::COMPLETE, CallLogStatus::BUSY, CallLogStatus::NOT_ANSWERED]]);
        $parentQuery->andWhere(['OR',
            ['cl_category_id' => null],
            ['<>', 'cl_category_id', Call::SOURCE_TRANSFER_CALL]
        ]);

        if ($this->projectIds) {
            $parentQuery->andWhere(['cl_project_id' => $this->projectIds]);
        }

        if ($this->dep_ids) {
            $parentQuery->andWhere(['cl_department_id' => $this->dep_ids]);
        }

        if ($this->cl_user_id) {
            $parentQuery->andWhere(['cl_user_id' => $this->cl_user_id]);
        } else if ($this->userGroupIds) {
            $userIdsByGroup = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id) as cl_user_id'])->where(['ugs_group_id' => $this->userGroupIds])->asArray()->all();
            if ($userIdsByGroup) {
                $parentQuery->andWhere(['in', ['cl_user_id'], $userIdsByGroup]);
            }
        }

        if ($this->recordingDurationFrom) {
            $parentQuery->andWhere(['>=', 'cl_duration', $this->recordingDurationFrom]);
        }

        if ($this->recordingDurationTo) {
            $parentQuery->andWhere(['<=', 'cl_duration', $this->recordingDurationTo]);
        }

        if ($this->betweenHoursFrom) {
            $parentQuery->andWhere(['>=', 'hour(convert_tz(cl_call_created_dt, \'+00:00\', \''.$timeZone.'\'))', $this->betweenHoursFrom]);
        }

        if ($this->betweenHoursTo) {
            $parentQuery->andWhere(['<=', 'hour(convert_tz(cl_call_created_dt, \'+00:00\', \''.$timeZone.'\'))', $this->betweenHoursTo]);
        }

        $parentQuery->groupBy([new \yii\db\Expression('1,2')]);

        $childQuery = self::find()->select([
            ''. $this->setGroupingParam() .' AS group',
            '"total" AS `callType`',
            'count(*) as `totalCalls`',
            'count(*)/count(distinct(DATE_FORMAT(cl_call_created_dt, \'%Y-%m-%d\'))) `avgCallsPerGroup`',
            'sum(cl_duration)as `totalCallsDuration`',
            'avg(cl_duration) as `avgCallDuration`'
        ]);

        $childQuery->from([new \yii\db\Expression(CallLog::tableName(). ' PARTITION('. $this->getPartitionsByYears() .') ')]);

        $childQuery->andWhere([
            'between',
            'cl_call_created_dt',
            Employee::convertTimeFromUserDtToUTC(strtotime($this->createTimeStart)),
            Employee::convertTimeFromUserDtToUTC(strtotime($this->createTimeEnd))
            ]);

        $childQuery->andWhere(['cl_status_id' => [CallLogStatus::COMPLETE, CallLogStatus::BUSY, CallLogStatus::NOT_ANSWERED]]);
        $childQuery->andWhere(['OR',
            ['cl_category_id' => null],
            ['<>', 'cl_category_id', Call::SOURCE_TRANSFER_CALL]
        ]);

        if ($this->projectIds) {
            $childQuery->andWhere(['cl_project_id' => $this->projectIds]);
        }

        if ($this->dep_ids) {
            $childQuery->andWhere(['cl_department_id' => $this->dep_ids]);
        }

        if ($this->cl_user_id) {
            $childQuery->andWhere(['cl_user_id' => $this->cl_user_id]);
        } else if ($this->userGroupIds) {
            $userIdsByGroup = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id) as cl_user_id'])->where(['ugs_group_id' => $this->userGroupIds])->asArray()->all();
            if ($userIdsByGroup) {
                $childQuery->andWhere(['in', ['cl_user_id'], $userIdsByGroup]);
            }
        }

        if ($this->recordingDurationFrom) {
            $childQuery->andWhere(['>=', 'cl_duration', $this->recordingDurationFrom]);
        }

        if ($this->recordingDurationTo) {
            $childQuery->andWhere(['<=', 'cl_duration', $this->recordingDurationTo]);
        }

        if ($this->betweenHoursFrom) {
            $childQuery->andWhere(['>=', 'hour(convert_tz(cl_call_created_dt, \'+00:00\', \''.$timeZone.'\'))', $this->betweenHoursFrom]);
        }

        if ($this->betweenHoursTo) {
            $childQuery->andWhere(['<=', 'hour(convert_tz(cl_call_created_dt, \'+00:00\', \''.$timeZone.'\'))', $this->betweenHoursTo]);
        }

        $childQuery->groupBy([new \yii\db\Expression('1')]);

        $parentQuery->union($childQuery);

        return new SqlDataProvider(['sql' => $parentQuery->createCommand()->rawSql, 'pagination' => false]);
    }

    private function setGroupingParam()
    {
        $timeZone = Employee::getUtcOffsetDst($this->timeZone, $this->createTimeStart);

        $dateFormat = $this->getDateFormat($this->callGraphGroupBy) ?? $this->getDefaultDateFormat();
        if ((int)$this->callGraphGroupBy === self::DATE_FORMAT_WEEKS) {
            return "concat(str_to_date(date_format(convert_tz(cl_call_created_dt, '+00:00', '".$timeZone."'), '%Y %v Monday'), '%x %v %W'), '/', str_to_date(date_format(convert_tz(cl_call_created_dt, '+00:00', '".$timeZone."'), '%Y %v Sunday'), '%x %v %W'))";
        } if ((int)$this->callGraphGroupBy === self::DATE_FORMAT_WEEKDAYS){
            return "WEEKDAY(convert_tz(cl_call_created_dt, '+00:00', '".$timeZone."'))";
        } else {
            return "date_format(convert_tz(cl_call_created_dt, '+00:00', '".$timeZone."'), '$dateFormat')";
        }
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