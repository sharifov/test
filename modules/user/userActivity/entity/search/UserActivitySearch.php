<?php

namespace modules\user\userActivity\entity\search;

use common\models\Employee;
use kartik\daterange\DateRangeBehavior;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleQuery;
use modules\user\src\events\UserEvents;
use modules\user\userActivity\service\UserActivityService;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\user\userActivity\entity\UserActivity;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use yii\helpers\VarDumper;

/**
 * UserActivitySearch represents the model behind the search form of `modules\user\userActivity\entity\UserActivity`.
 */
class UserActivitySearch extends UserActivity
{
    public $defaultUserTz;
    public $reportTimezone;

    public string $dateTimeRange = '';
    public string $timeStart = '';
    public string $timeEnd = '';

    public string $clientStartDate = '';
    public string $clientEndDate = '';
//    public string $startedDateRange = '';
//    public string $endedDateRange = '';

    private string $defaultDTStart;
    private string $defaultDTEnd;

    public function __construct(int $defaultMonth = 1, string $formatDt = 'Y-m-d', array $config = [])
    {
        $this->defaultDTEnd = (new \DateTime())->format($formatDt);
        $this->defaultDTStart = (new \DateTimeImmutable())
            ->modify('-' . abs($defaultMonth) . ' months')->format($formatDt);

        parent::__construct($config);
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => DateRangeBehavior::class,
                'attribute' => 'dateTimeRange',
                'dateStartAttribute' => 'timeStart',
                'dateEndAttribute' => 'timeEnd',
            ],
        ];
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['ua_user_id', 'ua_object_id', 'ua_type_id', 'ua_shift_event_id'], 'integer'],
            [['ua_object_event', 'ua_start_dt', 'ua_end_dt', 'ua_description'], 'safe'],
            [['defaultUserTz', 'reportTimezone'], 'string'],
            [['dateTimeRange'], 'default', 'value' => $this->defaultDTStart . ' - ' . $this->defaultDTEnd],
            [['dateTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['timeStart', 'timeEnd'], 'safe'],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'uf_title' => 'Title',
            'uf_message' => 'Message',
            'dateTimeRange' => 'Date Time Range',
        ];
    }

    public function formName()
    {
        return 'UserActivitySearch';
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
        $query = UserActivity::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'ua_start_dt' => SORT_DESC,
                ]
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ua_user_id' => $this->ua_user_id,
            'ua_object_id' => $this->ua_object_id,
            'ua_start_dt' => $this->ua_start_dt,
            'ua_end_dt' => $this->ua_end_dt,
            'ua_type_id' => $this->ua_type_id,
            'ua_shift_event_id' => $this->ua_shift_event_id,
        ]);

        $query->andFilterWhere(['like', 'ua_object_event', $this->ua_object_event])
            ->andFilterWhere(['like', 'ua_description', $this->ua_description]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @param string|null $defaultStartTime
     * @param string|null $defaultEndTime
     * @return array
     */
    public function searchUserActivity(
        $params,
        Employee $user,
        ?string $defaultStartTime = null,
        ?string $defaultEndTime = null
    ): array {

        if (empty($defaultStartTime)) {
            $defaultStartTime = strtotime('-1 days');
        }

        if (empty($defaultEndTime)) {
            $defaultEndTime = time();
        }

        $this->load($params);
        $timezone = $user->timezone;

        if ($this->reportTimezone == null) {
            $this->defaultUserTz = $timezone;
        } else {
            $timezone = $this->reportTimezone;
            $this->defaultUserTz = $this->reportTimezone;
        }

        if ($this->dateTimeRange != null) {
            $dates = explode(' - ', $this->dateTimeRange);
            $hourSub = date('G', strtotime($dates[0]));
            //$timeSub = date('G', strtotime($this->timeFrom));

            $startDateTime = Employee::convertToUTC(strtotime($dates[0]), $this->defaultUserTz); //- ($hourSub * 3600)
            $endDateTime = Employee::convertToUTC(strtotime($dates[1]), $this->defaultUserTz);

            //$startDateTimeCalendar = (strtotime($dates[0]), $this->defaultUserTz); //- ($hourSub * 3600)
            //$endDateTime = Employee::convertToUTC(strtotime($dates[1]), $this->defaultUserTz);

            $startDateTimeCalendar = date('Y-m-d H:i', strtotime($dates[0]));
            $endDateTimeCalendar = date('Y-m-d H:i', strtotime($dates[1]));

            //$between_condition = " BETWEEN '{$date_from}' AND '{$date_to}'";
            //$utcOffsetDST = Employee::getUtcOffsetDst($timezone, $date_from) ?? date('P');
        } else {
            //$timeSub = date('G', strtotime(date('00:00')));

            $startDateTime = date('Y-m-d H:i', $defaultStartTime);
            $endDateTime = date('Y-m-d H:i', $defaultEndTime);

//            $startDateTime = Employee::convertToUTC($defaultStartTime, $this->defaultUserTz); //- ($hourSub * 3600)
//            $endDateTime = Employee::convertToUTC($defaultEndTime, $this->defaultUserTz);

            $startDateTimeCalendar = Employee::convertTimeFromUtcToUserTime($this->defaultUserTz, strtotime($startDateTime));
            $endDateTimeCalendar = Employee::convertTimeFromUtcToUserTime($this->defaultUserTz, strtotime($endDateTime));

            //$date_from = Employee::convertToUTC(strtotime(date('Y-m-d 00:00') . ' -2 days'), $this->defaultUserTz);
            //$date_to = Employee::convertToUTC(strtotime(date('Y-m-d 23:59')), $this->defaultUserTz);
            //$between_condition = " BETWEEN '{$date_from}' AND '{$date_to}'";
            //$utcOffsetDST = Employee::getUtcOffsetDst($timezone, $date_from) ?? date('P');
        }

        $userOnCallEvents = UserActivityService::getUniteEventsByUserId(
            $user->id,
            date('Y-m-d H:i:s', strtotime($startDateTime)),
            date('Y-m-d H:i:s', strtotime($endDateTime)),
            UserEvents::EVENT_ON_CALL,
            1,
            1,
            'on_call'
        );

        //VarDumper::dump($userOnCallEvents, 10, true); exit;

        $scheduleEventList = UserShiftScheduleQuery::getExistEventList(
            $user->id,
            $startDateTime,
            $endDateTime,
            null,
            [ShiftScheduleType::SUBTYPE_WORK_TIME]
        );


        $userOnlineEvents = UserActivityService::getUniteEventsByUserId(
            $user->id,
            date('Y-m-d H:i:s', strtotime($startDateTime)),
            date('Y-m-d H:i:s', strtotime($endDateTime)),
            UserEvents::EVENT_ONLINE,
            5,
            3,
            'online'
        );


        $beforeMin = 60;
        $afterMin = 60;
        $userOnlineData = [];
        $summaryData = [];

        if ($scheduleEventList) {
            foreach ($scheduleEventList as $item) {
                $startDateTimeUTC = $item['uss_start_utc_dt'];
                $endDateTimeUTC = $item['uss_end_utc_dt'];

                $lateArrivalStartDateTime = date('Y-m-d H:i:s', strtotime($startDateTimeUTC) - $beforeMin * 60);
                $overtimeEndDateTime = date('Y-m-d H:i:s', strtotime($endDateTimeUTC) + $afterMin * 60);

                $earlyStart = UserActivityService::getUniteEventsByUserId(
                    $user->id,
                    $lateArrivalStartDateTime,
                    $startDateTimeUTC,
                    UserEvents::EVENT_ONLINE,
                    5,
                    3,
                    'EarlyStart'
                );

                $lateFinish = UserActivityService::getUniteEventsByUserId(
                    $user->id,
                    $endDateTimeUTC,
                    $overtimeEndDateTime,
                    UserEvents::EVENT_ONLINE,
                    5,
                    3,
                    'LateFinish'
                );

                $usefulTime = UserActivityService::getUniteEventsByUserId(
                    $user->id,
                    $startDateTimeUTC,
                    $endDateTimeUTC,
                    UserEvents::EVENT_ONLINE,
                    5,
                    3,
                    'UsefulTime'
                );

                $earlyFinish = [];
                if ($usefulTime && !empty($usefulTime[0])) {
                    $firstEvent = $usefulTime[0];
                    $earlyFinish = [
                        [
                            'start' => $startDateTimeUTC,
                            //'end' => date('Y-m-d H:i:s', strtotime($firstEvent['start'])),
                            'end' => $firstEvent['start'],
                            'duration' => (int) (strtotime($firstEvent['start']) - strtotime($startDateTimeUTC)) / 60,
                            'type' => 'EarlyFinish'
                        ]
                    ];
                }

                $lateStart = [];
                if ($usefulTime) {
                    $lastEvent = end($usefulTime);
                    $lateStart = [
                        [
                            'start' => $lastEvent['end'],
                            //'start' => date('Y-m-d H:i:s', strtotime($lastEvent['end'])),
                            'end' => $endDateTimeUTC,
                            'duration' => (int) (strtotime($endDateTimeUTC) - strtotime($lastEvent['end'])) / 60,
                            'type' => 'LateStart'
                        ]
                    ];
                }

                //$userOnlineData2[] = array_merge($earlyStart, $overtime, $earlyLeave, $earlyArrival);

                $userOnlineData[$item['uss_id']] = [
                    'earlyStart' => $earlyStart,
                    'lateFinish' => $lateFinish,
                    'usefulTime' => $usefulTime,
                    'earlyFinish' => $earlyFinish,
                    'lateStart' => $lateStart,
                ];

                $summaryData[$item['uss_id']] = array_merge($earlyStart, $earlyFinish, $lateStart, $lateFinish, $usefulTime);
            }
        }

        $summary = [];
        if ($summaryData) {
            foreach ($summaryData as $events) {
                foreach ($events as $event) {
                    if (isset($summary[$event['type']])) {
                        $summary[$event['type']] += $event['duration'];
                    } else {
                        $summary[$event['type']] = $event['duration'];
                    }
                }
            }
        }

        // VarDumper::dump($summary, 10, true); exit;

        $userActiveEvents = UserActivityService::getUniteEventsByUserId(
            $user->id,
            date('Y-m-d H:i:s', strtotime($startDateTime)),
            date('Y-m-d H:i:s'),
            UserEvents::EVENT_ACTIVE,
            3,
            3,
            'activity'
        );

        if ($userOnlineEvents) {
            //foreach ($userOnlineEvents as $events) {
            // VarDumper::dump($events, 10, true);
            foreach ($userOnlineEvents as $event) {
                if (isset($summary[$event['type']])) {
                    $summary[$event['type']] += $event['duration'];
                } else {
                    $summary[$event['type']] = $event['duration'];
                }
            }
            //}
        }

        if ($userActiveEvents) {
            foreach ($userActiveEvents as $event) {
                if (isset($summary[$event['type']])) {
                    $summary[$event['type']] += $event['duration'];
                } else {
                    $summary[$event['type']] = $event['duration'];
                }
            }
        }

        if ($userOnCallEvents) {
            foreach ($userOnCallEvents as $event) {
                if (isset($summary[$event['type']])) {
                    $summary[$event['type']] += $event['duration'];
                } else {
                    $summary[$event['type']] = $event['duration'];
                }
            }
        }


        $data = [
            'startDateTime' => $startDateTime,
            'endDateTime' => $endDateTime,
            'startDateTimeCalendar' => $startDateTimeCalendar,
            'endDateTimeCalendar' => $endDateTimeCalendar,
            'scheduleEventList' => $scheduleEventList,
            'userActiveEvents' => $userActiveEvents,
            'userOnlineEvents' => $userOnlineEvents,
            'userOnCallEvents' => $userOnCallEvents,
            'userOnlineData' => $userOnlineData,
            'summary' => $summary,
        ];

        return $data;
    }
}
