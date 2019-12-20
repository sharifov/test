<?php

namespace sales\entities\call;

use common\models\Call;
use common\models\CallQuery;
use common\models\Employee;
use common\models\search\CallSearch;
use common\models\UserGroupAssign;
use kartik\daterange\DateRangeBehavior;
use yii\data\ActiveDataProvider;
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
 * @property string $timeZone
 */
class CallGraphsSearch extends CallSearch
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
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['c_id', 'c_call_type_id', 'c_lead_id', 'c_created_user_id', 'c_com_call_id', 'c_project_id', 'c_is_new', 'c_is_deleted', 'supervision_id', 'limit', 'c_recording_duration',
				'c_source_type_id', 'call_duration_from', 'call_duration_to', 'c_case_id', 'c_client_id', 'c_status_id', 'callDepId', 'userGroupId', 'recordingDurationFrom', 'recordingDurationTo', 'betweenHoursFrom', 'betweenHoursTo', 'callGraphGroupBy', 'chartTotalCallsVaxis'], 'integer'],
			[['c_call_sid', 'c_account_sid', 'c_from', 'c_to', 'c_sip', 'c_call_status', 'c_api_version', 'c_direction', 'c_forwarded_from', 'c_caller_name', 'c_parent_call_sid', 'c_call_duration', 'c_sip_response_code', 'c_recording_url', 'c_recording_sid',
				'c_timestamp', 'c_uri', 'c_sequence_number', 'c_created_dt', 'c_updated_dt', 'c_error_message', 'c_price', 'statuses', 'limit', 'projectId', 'statusId', 'callTypeId'], 'safe'],
			[['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
			[['ug_ids', 'status_ids', 'dep_ids', 'totalChartColumns', 'projectIds', 'userGroupIds'], 'each', 'rule' => ['integer']],
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
			'coalesce(sum(tbl.incoming_duration_sum + tbl.outgoing_duration_sum) / sum(incoming + outgoing),0) as total_rec_duration_avg'
		]);

		if ((int)$this->callGraphGroupBy === self::DATE_FORMAT_WEEKS) {
			$query->addSelect(["concat(str_to_date(date_format(created, '%Y %v Monday'), '%x %v %W'), ' - ', str_to_date(date_format(created, '%Y %v Sunday'), '%x %v %W')) as created_formatted"]);
//			$query->addSelect(["sum(incoming) / count(distinct str_to_date(date_format(created, '%Y %v Monday'), '%x %v %W'), ' - ', str_to_date(date_format(created, '%Y %v Sunday'), '%x %v %W')) as 'incoming_avg'"]);
//			$query->addSelect(["sum(outgoing) / count(distinct str_to_date(date_format(created, '%Y %v Monday'), '%x %v %W'), ' - ', str_to_date(date_format(created, '%Y %v Sunday'), '%x %v %W')) as 'outgoing_avg'"]);
//			$query->addSelect(["sum(incoming + outgoing) / count(distinct date_format(created, '%Y-%m-%d')) as total_calls_avg"]);
		} else {
			$query->addSelect(["date_format(`created`, '$dateFormat') as created_formatted"]);
//			$query->addSelect(['sum(incoming) / count(distinct date_format(created, \''.self::DATE_FORMAT_LIST_COUNT[$this->callGraphGroupBy].'\')) as incoming_avg']);
//			$query->addSelect(['sum(outgoing) / count(distinct date_format(created, \''.self::DATE_FORMAT_LIST_COUNT[$this->callGraphGroupBy].'\')) as outgoing_avg']);
//			$query->addSelect(['sum(incoming + outgoing) / count(distinct date_format(created, \''.self::DATE_FORMAT_LIST_COUNT[$this->callGraphGroupBy].'\')) as total_calls_avg']);
		}


		if ($this->createTimeRange) {
			$this->createTimeStart = date('Y-m-d H:i:00', $this->createTimeStart);
			$this->createTimeEnd = date('Y-m-d H:i:59', $this->createTimeEnd);
		} else {
			$this->createTimeStart = date('Y-m-d 00:00:00', strtotime(self::CREATE_TIME_START_DEFAULT));
			$this->createTimeEnd = date('Y-m-d H:i:s');
			$this->createTimeRange = $this->createTimeStart . ' - ' . $this->createTimeEnd;
		}

		if (!$this->c_recording_duration) {
			$this->c_recording_duration = 2;
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

	/**
	 * @return CallQuery
	 */
	private function getCompleteIncomingCallsQuery(): CallQuery
	{
		$query = Call::find()
			->select([
				'count(c_id) as incoming',
				'coalesce(0) as outgoing',
				'coalesce(sum(c_recording_duration), 0) as incoming_duration_sum',
				'coalesce(0) as outgoing_duration_sum'
			])
			->andWhere(['c_call_type_id' => self::CALL_TYPE_IN])
			->andWhere(['c_status_id' => self::STATUS_COMPLETED])
			->andWhere(['not', ['c_parent_id' => null]]);

		if ($this->recordingDurationFrom) {
			$query->andWhere(['>=', 'c_recording_duration', $this->recordingDurationFrom]);
		}

		if ($this->recordingDurationTo) {
			$query->andWhere(['<=', 'c_recording_duration', $this->recordingDurationTo]);
		}

		return $this->applySearchQuery($query);
	}

	/**
	 * @return CallQuery
	 */
	private function getNotAnsweredIncomingCallsQuery(): CallQuery
	{
		$query = Call::find()
			->select([
				'count(c_id) as incoming',
				'coalesce(0) as outgoing',
				'coalesce(sum(c_recording_duration), 0) as incoming_duration_sum',
				'coalesce(0) as outgoing_duration_sum'
			])
			->andWhere(['c_call_type_id' => self::CALL_TYPE_IN])
			->andWhere(['c_status_id' => [self::STATUS_BUSY, self::STATUS_NO_ANSWER]])
			->andWhere(['c_parent_id' => null]);

		if ($this->recordingDurationFrom) {
			$query->andWhere(['>=', 'c_recording_duration', $this->recordingDurationFrom]);
		}

		if ($this->recordingDurationTo) {
			$query->andWhere(['<=', 'c_recording_duration', $this->recordingDurationTo]);
		}

		return $this->applySearchQuery($query);
	}

	/**
	 * @return CallQuery
	 */
	private function getCompleteOutgoingCallsQuery(): CallQuery
	{
		$query = Call::find()
			->select([
				'coalesce(0) as incoming',
				'count(c_id) as outgoing',
				'coalesce(0) as incoming_duration_sum',
				'coalesce(sum(c_recording_duration), 0) as outgoing_duration_sum'
			])
			->andWhere(['c_call_type_id' => self::CALL_TYPE_OUT])
			->andWhere(['c_status_id' => self::STATUS_COMPLETED])
			->andWhere(['not', ['c_parent_id' => null]]);

		if ($this->recordingDurationFrom) {
			$query->andWhere(['>=', 'c_recording_duration', $this->recordingDurationFrom]);
		}

		if ($this->recordingDurationTo) {
			$query->andWhere(['<=', 'c_recording_duration', $this->recordingDurationTo]);
		}

		return $this->applySearchQuery($query);
	}

	/**
	 * @return CallQuery
	 */
	private function getNotAnsweredOutgoingCallsQuery(): CallQuery
	{
		$query = Call::find()
			->select([
				'coalesce(0) as incoming',
				'count(c_id) as outgoing',
				'coalesce(0) as incoming_duration_sum',
				'coalesce(sum(c_recording_duration), 0) as outgoing_duration_sum'
			])
			->andWhere(['c_call_type_id' => self::CALL_TYPE_OUT])
			->andWhere(['c_status_id' => [self::STATUS_BUSY, self::STATUS_NO_ANSWER]])
			->andWhere(['not', ['c_parent_id' => null]]);

		return $this->applySearchQuery($query);
	}

	/**
	 * @param CallQuery $query
	 * @return CallQuery
	 */
	private function applySearchQuery(CallQuery $query): CallQuery
	{
		$timeZone = Employee::getUtcOffsetDst($this->timeZone, date('Y-m-d'));

		$query->addSelect(["date_format(convert_tz(c_created_dt, '+00:00', '$timeZone'), '%Y-%m-%d %H:00:00') as created"]);

		$query->andWhere(['between', "convert_tz(c_created_dt, '+00:00', '".$timeZone."')", $this->createTimeStart, $this->createTimeEnd]);

		if ($this->projectIds) {
			$query->andWhere(['c_project_id' => $this->projectIds]);
		}

		if ($this->dep_ids) {
			$query->andWhere(['c_dep_id' => $this->dep_ids]);
		}

		if ($this->c_created_user_id) {
			$query->andWhere(['c_created_user_id' => $this->c_created_user_id]);
		} else if ($this->userGroupIds) {
			$userIdsByGroup = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id) as c_created_user_id'])->where(['ugs_group_id' => $this->userGroupIds])->asArray()->all();
			if ($userIdsByGroup) {
				$query->andWhere(['in', ['c_created_user_id'], $userIdsByGroup]);
			}
		}

		if ($this->betweenHoursFrom) {
			$query->andWhere(['>=', 'hour(c_created_dt)', $this->betweenHoursFrom]);
		}

		if ($this->betweenHoursTo) {
			$query->andWhere(['<=', 'hour(c_created_dt)', $this->betweenHoursTo]);
		}

		$query->groupBy('created')->orderBy('created');

		return $query;
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
		return self::getChartTotalCallsVaxisTextList()[$vaxisId] ?? null;
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