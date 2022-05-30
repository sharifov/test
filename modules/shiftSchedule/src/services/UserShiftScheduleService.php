<?php

namespace modules\shiftSchedule\src\services;

use common\models\Employee;
use Cron\CronExpression;
use Exception;
use frontend\helpers\TimeConverterHelper;
use modules\shiftSchedule\src\entities\shift\Shift;
use modules\shiftSchedule\src\entities\shiftScheduleRule\ShiftScheduleRule;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\shiftScheduleTypeLabelAssign\ShiftScheduleTypeLabelAssign;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleQuery;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleRepository;
use modules\shiftSchedule\src\forms\ShiftScheduleCreateForm;
use modules\shiftSchedule\src\forms\ShiftScheduleEditForm;
use modules\shiftSchedule\src\forms\SingleEventCreateForm;
use modules\shiftSchedule\src\forms\UserShiftCalendarMultipleUpdateForm;
use modules\shiftSchedule\src\helpers\UserShiftScheduleHelper;
use src\auth\Auth;
use Yii;
use yii\helpers\VarDumper;

class UserShiftScheduleService
{
    private UserShiftScheduleRepository $repository;

    public function __construct(UserShiftScheduleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $userId
     * @return int
     */
    public static function removeDataByUser(int $userId): int
    {
        return UserShiftSchedule::deleteAll(['uss_user_id' => $userId]);
    }

    /**
     * @param int $userId
     * @param string $startDt
     * @param string $endDt
     * @return array|UserShiftSchedule[]
     */
    public static function getTimelineListByUser(int $userId, string $startDt, string $endDt): array
    {
        return UserShiftSchedule::find()
            ->where(['uss_user_id' => $userId])
            ->andWhere(['AND',
                ['>=', 'uss_start_utc_dt', date('Y-m-d H:i:s', strtotime($startDt))],
                ['<=', 'uss_start_utc_dt', date('Y-m-d H:i:s', strtotime($endDt))]
            ])
            ->all();
    }

    /**
     * @param int $userId
     * @param string $minDate
     * @param string $maxDate
     * @param array $statusList
     * @return array|UserShiftSchedule[]
     */
    public static function getUserShiftScheduleDataStats(
        int $userId,
        string $minDate,
        string $maxDate,
        array $statusList = []
    ): array {
        $query = UserShiftSchedule::find()
            ->select(['uss_sst_id', 'uss_year' => 'YEAR(uss_start_utc_dt)',
                'uss_month' => 'MONTH(uss_start_utc_dt)',
                'uss_cnt' => 'COUNT(*)',
                'uss_duration' => 'SUM(uss_duration)',
            ])
            ->where(['uss_user_id' => $userId])
            ->andWhere(['AND',
                ['>=', 'uss_start_utc_dt', $minDate],
                ['<=', 'uss_start_utc_dt', $maxDate]
            ])

            ->groupBy(['uss_sst_id', 'uss_year', 'uss_month'])
            ->asArray();

        if ($statusList) {
            $query->andWhere(['uss_status_id' => $statusList]);
        }

        return $query->all();
    }

    /**
     * @param int $userId
     * @param string $minDate
     * @param string $maxDate
     * @param array $statusList
     * @return array|UserShiftSchedule[]
     */
    public static function getUserShiftScheduleLabelDataStats(
        int $userId,
        string $minDate,
        string $maxDate,
        array $statusList = []
    ): array {
        $query = UserShiftSchedule::find()
            ->select(['stl_key' => 'tla_stl_key', 'uss_year' => 'YEAR(uss_start_utc_dt)',
                'uss_month' => 'MONTH(uss_start_utc_dt)',
                'uss_cnt' => 'COUNT(*)',
                'uss_duration' => 'SUM(uss_duration)',
            ])
            ->alias('uss')
            //->with(['shiftScheduleType'])
            //->innerJoin(ShiftScheduleType::tableName(), 'sst_id=uss.uss_sst_id')
            ->innerJoin(ShiftScheduleTypeLabelAssign::tableName(), 'uss.uss_sst_id = tla_sst_id')
            // ['tla_stl_key', 'tla_sst_id']
            ->where(['uss_user_id' => $userId])
            ->andWhere(['AND',
                ['>=', 'uss_start_utc_dt', $minDate],
                ['<=', 'uss_start_utc_dt', $maxDate]
            ])

            ->groupBy(['tla_stl_key', 'uss_year', 'uss_month'])
            ->asArray();

        if ($statusList) {
            $query->andWhere(['uss_status_id' => $statusList]);
        }

        return $query->all();
    }

    /**
     * @param UserShiftSchedule[] $timelineList
     * @return array
     */
    public static function getCalendarTimelineJsonData(array $timelineList): array
    {
        $data = [];
        if ($timelineList) {
            foreach ($timelineList as $item) {
                $dataItem = [
                    'id' => $item->uss_id,
                    //groupId: '999',
                    'title' => $item->getScheduleTypeKey(), // . '-' . $item->uss_id,
                        //. ' - ' . date('d H:i', strtotime($item->uss_start_utc_dt)),
                    'description' => $item->getScheduleTypeTitle() . "\r\n" . '(' . $item->uss_id . ')' . ', duration: ' .
                        Yii::$app->formatter->asDuration($item->uss_duration * 60),
                    //. "\r\n" . $item->uss_description,
                    'start' => date('c', strtotime($item->uss_start_utc_dt)),
                    'end' => date('c', strtotime($item->uss_end_utc_dt)),
                    'color' => $item->shiftScheduleType ? $item->shiftScheduleType->sst_color : 'gray',

                    'display' => 'block', // 'list-item' , 'background'
                    //'textColor' => 'black' // an option!
                    'extendedProps' => [
                        'icon' => $item->shiftScheduleType->sst_icon_class ?? '',
                    ]
                ];

                if (
                    !in_array($item->uss_status_id, [UserShiftSchedule::STATUS_DONE,
                    UserShiftSchedule::STATUS_APPROVED])
                ) {
                    $dataItem['borderColor'] = '#000000';
                    $dataItem['title'] .= ' (' . $item->getStatusName() . ')';
                    $dataItem['description'] .= ' (' . $item->getStatusName() . ')';
                    //$data[$item->uss_id]['extendedProps']['icon'] = 'rgb(255,0,0)';
                }

                $data[] = $dataItem;
            }
        }
        return $data;
    }

    /**
     * @param int $userId
     * @param int $days
     * @return int
     * @throws Exception
     */
    public static function generateExampleDataByUser(int $userId, int $days = 90): int
    {
        $statuses = array_keys(UserShiftSchedule::getTypeList());
        $shiftList = array_keys(Shift::getList());
        $shiftRuleList = array_keys(ShiftScheduleRule::getList());
        $shiftTypeList = array_keys(ShiftScheduleType::getList());

        $minutes = [0, 10, 20, 30, 40];

        //VarDumper::dump($statuses, 10, true); exit;

        $cnt = 0;

        for ($i = 1; $i <= $days; $i++) {
            $hour = random_int(12, 22);
            $min = $minutes[random_int(1, count($minutes)) - 1];
            $timeStart = mktime($hour, $min, 0, date("m") - 1, $i, date("Y"));
            $duration = random_int(2, 8) * 60 + $minutes[random_int(1, count($minutes)) - 1];
            $timeEnd = mktime($hour, $duration + $min, 0, date("m") - 1, $i, date("Y"));


            $statusId = $statuses[random_int(0, count($statuses) - 1)];
            $shiftId = $shiftList[random_int(0, count($shiftList) - 1)];
            $shiftRuleId = $shiftRuleList[random_int(0, count($shiftRuleList) - 1)];
            $shiftTypeId = $shiftTypeList[random_int(0, count($shiftTypeList) - 1)];

            $tl = new UserShiftSchedule();
            $tl->uss_user_id = $userId;
            $tl->uss_sst_id = $shiftTypeId;
            $tl->uss_type_id = random_int(1, 2);
            $tl->uss_start_utc_dt = date('Y-m-d H:i:s', $timeStart);
            $tl->uss_end_utc_dt = date('Y-m-d H:i:s', $timeEnd);
            $tl->uss_duration = $duration;
            $tl->uss_ssr_id = $shiftRuleId;
            $tl->uss_shift_id = $shiftId;
            $tl->uss_status_id = $statusId;
            $tl->uss_description = 'Description ' . $tl->uss_start_utc_dt;
            if ($tl->save()) {
                $cnt++;
            }
        }
        return $cnt;
    }

    /**
     * @param int $daysLimit
     * @param int $daysOffset
     * @param string|null $dateFrom
     * @param array $userList
     * @return array
     */
    public static function generateUserSchedule(
        int $daysLimit = 20,
        int $daysOffset = 0,
        ?string $dateFrom = null,
        array $userList = []
    ): array {
        if (empty($dateFrom)) {
            $dateFrom = date('Y-m-d');
        }

        $dateFromTs = strtotime($dateFrom);

        $m = (int) date("m", $dateFromTs);
        $d = (int) date("d", $dateFromTs);
        $y = (int) date("Y", $dateFromTs);

        //$cnt = 0;
        $data = [];
        $rules = ShiftScheduleRule::find()->where(['ssr_enabled' => true])->all();


        $minDate = date('Y-m-d H:i:s', mktime(0, 0, 0, $m, ($d + $daysOffset), $y));
        $maxDate = date('Y-m-d H:i:s', mktime(0, 0, 0, $m, ($d + $daysLimit + $daysOffset), $y));

        $scheduleData = UserShiftSchedule::find()
            ->select(['uss_user_id', 'uss_ssr_id', 'uss_start_utc_dt'])
            ->andWhere(['AND',
                ['>=', 'uss_start_utc_dt', $minDate],
                ['<=', 'uss_start_utc_dt', $maxDate]
            ])
            ->groupBy(['uss_user_id', 'uss_ssr_id', 'uss_start_utc_dt'])
            ->asArray()
            ->all();

        $timeLineData = [];

        if ($scheduleData) {
            foreach ($scheduleData as $item) {
                $timeLineData[$item['uss_user_id']][$item['uss_ssr_id']][$item['uss_start_utc_dt']] = true;
            }
        }


//        VarDumper::dump($timeLineData, 10, true); exit;


        if ($rules) {
            for ($i = $daysOffset; $i < ($daysLimit + $daysOffset); $i++) {
                $dateTs = mktime(0, 0, 0, $m, ($d + $i), $y);
                $dateTime = date('Y-m-d H:i:s', $dateTs);
                $date = date('Y-m-d', $dateTs);

                foreach ($rules as $rule) {
                    $expression = $rule->getCronExpression();
                    if (empty($expression)) {
                        continue;
                    }
                    //echo 'CRON Expression: ' . $expression . " - ";
                    $valid = CronExpression::isValidExpression($expression);
                    if (!$valid) {
                        $errorData['error'] = 'Invalid CRON Expression';
                        $errorData['data'] = $rule->attributes;
                        Yii::warning($errorData, 'UserShiftScheduleService:CronExpression:isValidExpression');
                        continue;
                    }
                    //echo 'Valid CRON Expression: ' . $expression . "\r\n";

                    $isDue = false;
                    if (self::isDueCronExpression($expression, $dateTime)) {
                        $isDue = true;
                        $expressionExclude = $rule->getCronExpressionExclude();
                        if (
                            $expressionExclude &&
                            self::isDueCronExpression($expressionExclude, $dateTime)
                        ) {
                            $isDue = false;
                        }
                    }

                    if ($isDue) {
                        if ($rule->shift && $rule->shift->sh_enabled) {
                            // echo $rule->shift->sh_id . "\r\n";
                            if ($rule->shift->userShiftAssigns) {
                                foreach ($rule->shift->userShiftAssigns as $user) {
                                    if (!empty($userList)) {
                                        if (!in_array($user->usa_user_id, $userList)) {
                                            continue;
                                        }
                                    }

                                    $timeStartSec = strtotime($date . ' ' . $rule->ssr_start_time_utc);
                                    $timeEndSec = strtotime($date . ' ' . $rule->ssr_end_time_utc);

                                    if ($timeStartSec > $timeEndSec) {
                                        $timeEndSec = $timeEndSec + (24 * 60 * 60);
                                    }

                                    $timeStart = date(
                                        'Y-m-d H:i:s',
                                        $timeStartSec
                                    );

                                    $timeEnd = date(
                                        'Y-m-d H:i:s',
                                        $timeEndSec
                                    );

                                    if (isset($timeLineData[$user->usa_user_id][$rule->ssr_id][$timeStart])) {
                                        continue;
                                    }

                                    $existEvenList = UserShiftScheduleService::getUserEventIdList(
                                        $user->usa_user_id,
                                        $timeStart,
                                        $timeEnd,
                                        [UserShiftSchedule::STATUS_APPROVED,
                                            UserShiftSchedule::STATUS_DONE, UserShiftSchedule::STATUS_CANCELED],
                                        [ShiftScheduleType::SUBTYPE_WORK_TIME, ShiftScheduleType::SUBTYPE_HOLIDAY]
                                    );

                                    if (empty($existEvenList)) {
                                        $data[$date][$user->usa_user_id][$rule->ssr_id] =
                                            self::createUserTimeLineByRule($rule, $user->usa_user_id, $date);
                                    } else {
                                        $dataInfo = [
                                            'message' => 'existEvenList',
                                            'data' => [
                                                'userId' => $user->usa_user_id,
                                                'date' => $date,
                                                'eventListId' => $existEvenList,
                                                'rule' => $rule->attributes
                                            ]
                                        ];
                                        Yii::info($dataInfo, 'info\UserShiftScheduleService:existEvenList');
                                    }
                                    //echo  '['.$date.'] Rule: ' . $rule->ssr_id . ' - shId: ' . $rule->shift->sh_id .
                                    // ' - user: ' . $user->usa_user_id . "\r\n";
                                }
                            }
                        }
                        // echo 'Is Due: ' . $rule->ssr_id . "\r\n";
                        //$cnt++;
                    }
                }
            }
        }
        return $data;
    }

    /**
     * @param ShiftScheduleRule $rule
     * @param int $userId
     * @param string $date
     * @return bool
     */
    public static function createUserTimeLineByRule(ShiftScheduleRule $rule, int $userId, string $date): bool
    {
        $tl = new UserShiftSchedule();
        $tl->uss_user_id = $userId;
        $tl->uss_sst_id = $rule->ssr_sst_id;
        $tl->uss_type_id = UserShiftSchedule::TYPE_AUTO;

        $timeStart = strtotime($date . ' ' . $rule->ssr_start_time_utc);
        $timeEnd = strtotime($date . ' ' . $rule->ssr_end_time_utc);


        if ($timeStart > $timeEnd) {
            $timeEnd = $timeEnd + (24 * 60 * 60);
        }

        $tl->uss_start_utc_dt = date('Y-m-d H:i:s', $timeStart);
        $tl->uss_end_utc_dt = date('Y-m-d H:i:s', $timeEnd);
        //$duration = (strtotime($tl->uss_end_utc_dt) - strtotime($tl->uss_end_utc_dt)) / 60;
        $tl->uss_duration = $rule->ssr_duration_time; //$duration;
        $tl->uss_ssr_id = $rule->ssr_id;
        $tl->uss_shift_id = $rule->ssr_shift_id;
        $tl->uss_status_id = UserShiftSchedule::STATUS_DONE;
        $tl->uss_customized = true;
        $tl->uss_description = 'CRON';
        if (!$tl->save()) {
            $errorData['errors'] = $tl->errors;
            $errorData['data'] = $tl->attributes;
            Yii::warning($errorData, 'UserShiftScheduleService:createUserTimeLineByRule:save');
            return false;
        }
        return true;
    }



    /**
     * @param mixed|null $expression
     * @param string $currentTime
     * @return bool
     */
    public static function isDueCronExpression(?string $expression, string $currentTime = 'now'): bool
    {
        if ($expression === null) {
            return false;
        }
        $cron = CronExpression::factory($expression);
        return $cron->isDue($currentTime);
    }

    public function createManual(ShiftScheduleCreateForm $form, ?string $userTimeZone): array
    {
        [$startDateTime, $endDateTime, $diffMinutes] = $this->generateEventTimeValues($form->dateTimeStart, $form->dateTimeEnd, $userTimeZone);

        $userShiftScheduleCreatedList = [];
        foreach ($form->getUsersBatch() as $user) {
            $userShiftSchedule = UserShiftSchedule::create(
                $user,
                $form->description,
                $startDateTime->format('Y-m-d H:i:s'),
                $endDateTime->format('Y-m-d H:i:s'),
                $diffMinutes,
                $form->status,
                UserShiftSchedule::TYPE_MANUAL,
                $form->scheduleType
            );
            $this->repository->save($userShiftSchedule);
            $userShiftScheduleCreatedList[] = $userShiftSchedule;
        }
        return $userShiftScheduleCreatedList;
    }

    /**
     * @param int $userId
     * @param string $startDateTime
     * @param string $endDateTime
     * @param array|null $statusListId
     * @param array|null $subTypeListId
     * @return array
     */
    public static function getExistEventIdList(
        int $userId,
        string $startDateTime,
        string $endDateTime,
        ?array $statusListId = [],
        ?array $subTypeListId = []
    ): array {

        $query = UserShiftSchedule::find();
        $query->alias('uss');
        $query->select(['uss.uss_id']);
        $query->where(['uss.uss_user_id' => $userId]);

        if (!empty($statusListId)) {
            $query->andWhere(['uss.uss_status_id' => $statusListId]);
        }

        if (!empty($subTypeListId)) {
            $query->innerJoin(ShiftScheduleType::tableName() . ' AS sst', 'sst.sst_id = uss.uss_sst_id');
            $query->andWhere(['sst.sst_subtype_id' => $subTypeListId]);
        }

        if (!empty($startDateTime) && !empty($endDateTime)) {
            $query->andWhere([
                'OR',
                ['between', 'uss.uss_start_utc_dt', $startDateTime, $endDateTime],
                ['between', 'uss.uss_end_utc_dt', $startDateTime, $endDateTime],
                [
                    'AND',
                    ['>=', 'uss.uss_start_utc_dt', $startDateTime],
                    ['<=', 'uss.uss_end_utc_dt', $endDateTime]
                ],
                [
                    'AND',
                    ['<=', 'uss.uss_start_utc_dt', $startDateTime],
                    ['>=', 'uss.uss_end_utc_dt', $endDateTime]
                ]
            ]);
        }

        return $query->column();
    }

    /**
     * @param int $userId
     * @param string $startDt
     * @param string $endDt
     * @param array|null $statusListId
     * @param array|null $subTypeListId
     * @return array
     */
    public static function getUserEventIdList(
        int $userId,
        string $startDt,
        string $endDt,
        ?array $statusListId = [],
        ?array $subTypeListId = []
    ): array {

        if ($statusListId === null) {
            $statusListId = [UserShiftSchedule::STATUS_APPROVED, UserShiftSchedule::STATUS_DONE];
        }

        if ($subTypeListId === null) {
            $subTypeListId = [ShiftScheduleType::SUBTYPE_WORK_TIME, ShiftScheduleType::SUBTYPE_HOLIDAY];
        }

        return self::getExistEventIdList($userId, $startDt, $endDt, $statusListId, $subTypeListId);
    }

    public function createSingleManual(SingleEventCreateForm $form, ?string $userTimeZone): UserShiftSchedule
    {
        [$startDateTime, $endDateTime, $diffMinutes] = $this->generateEventTimeValues($form->dateTimeStart, $form->dateTimeEnd, $userTimeZone);

        $userShiftSchedule = UserShiftSchedule::create(
            $form->userId,
            $form->description,
            $startDateTime->format('Y-m-d H:i:s'),
            $endDateTime->format('Y-m-d H:i:s'),
            $diffMinutes,
            $form->status,
            UserShiftSchedule::TYPE_MANUAL,
            $form->scheduleType
        );
        $this->repository->save($userShiftSchedule);
        return $userShiftSchedule;
    }

    public function edit(ShiftScheduleEditForm $form, UserShiftSchedule $event, ?string $timezone): void
    {
        [$startDateTime, $endDateTime, $diffMinutes] = $this->generateEventTimeValues($form->dateTimeStart, $form->dateTimeEnd, $timezone);

        $event->editFromCalendar(
            $form->status,
            $form->scheduleType,
            $startDateTime,
            $endDateTime,
            $diffMinutes,
            $form->description
        );

        try {
            $this->repository->save($event);
        } catch (\RuntimeException $e) {
            $form->addError('general', $e->getMessage());
        }
    }

    public function editMultiple(UserShiftCalendarMultipleUpdateForm $form, UserShiftSchedule $event, ?string $timezone): void
    {
        if (!empty($form->dateTimeStart && $form->dateTimeEnd)) {
            $start = $form->dateTimeStart;
            $end = $form->dateTimeEnd;
            [$startDateTime, $endDateTime, $diffMinutes] = $this->generateEventTimeValues($start, $end, $timezone);

            $event->uss_start_utc_dt = $startDateTime->format('Y-m-d H:i:s');
            $event->uss_end_utc_dt = $endDateTime->format('Y-m-d H:i:s');
            $event->uss_duration = $diffMinutes;
        }

        if (!empty($form->status)) {
            $event->uss_status_id = $form->status;
        }

        if (!empty($form->scheduleType)) {
            $event->uss_sst_id = $form->scheduleType;
        }

        if (!empty($form->description)) {
            $event->uss_description = $form->description;
        }

        $this->repository->save($event);
    }

    private function generateEventTimeValues(string $startDateTime, string $endDateTime, ?string $timezone): array
    {
        $startDateTime = new \DateTimeImmutable($startDateTime, $timezone ? new \DateTimeZone($timezone) : null);
        $startDateTime = $startDateTime->setTimezone(new \DateTimeZone('UTC'));
        $endDateTime = new \DateTimeImmutable($endDateTime, $timezone ? new \DateTimeZone($timezone) : null);
        $endDateTime = $endDateTime->setTimezone(new \DateTimeZone('UTC'));
        $interval = $startDateTime->diff($endDateTime);
        $diffMinutes = $interval->days * 24 * 60 + $interval->i + ($interval->h * 60);
        return [$startDateTime, $endDateTime, $diffMinutes];
    }
}
