<?php

namespace modules\shiftSchedule\src\reports;

use common\components\validators\IsArrayValidator;
use common\models\Department;
use common\models\Employee;
use common\models\UserDepartment;
use common\models\UserGroup;
use common\models\UserGroupAssign;
use modules\shiftSchedule\src\entities\shift\ShiftQuery;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use src\helpers\query\QueryHelper;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\validators\DateValidator;

class HeatMapAgentSearch extends Model
{
    public const DEFAULT_TIMEZONE = 'UTC';

    public $dateRange;
    public $userGroup;
    public $shifts;
    public $roles;
    public $cache;
    public $department;
    public $timeZone;

    private ?string $toDT = null;
    private ?string $fromDT = null;
    private string $toDefaultDT;
    private string $fromDefaultDT;
    private int $intervalDaysDefault = 30;

    /**
     * @throws \Exception
     */
    public function __construct(string $defaultTimeZone, array $config = [])
    {
        $this->timeZone = $defaultTimeZone;
        $this->setDataRangeDefault();
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['dateRange'], 'required'],
            [['dateRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['dateRange'], 'dateRangeProcessing'],

            [['userGroup'], IsArrayValidator::class],
            [['userGroup'], 'each', 'rule' => ['in', 'range' => array_keys(UserGroup::getList())], 'skipOnError' => true, 'skipOnEmpty' => true],

            [['shifts'], IsArrayValidator::class],
            [['shifts'], 'each', 'rule' => ['in', 'range' => array_keys(ShiftQuery::getList())], 'skipOnError' => true, 'skipOnEmpty' => true],

            [['department'], IsArrayValidator::class],
            [['department'], 'each', 'rule' => ['in', 'range' => array_keys(Department::getList())], 'skipOnError' => true, 'skipOnEmpty' => true],

            [['roles'], IsArrayValidator::class],

            ['timeZone', 'string'],
            ['timeZone', 'in', 'range' => array_keys(Employee::timezoneList(true))],
        ];
    }

    public function dateRangeProcessing($attribute): void
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
        if ($from >= $to) {
            $this->addError($attribute, 'Range From date more than To date');
            return;
        }

        $this->fromDT = $from->format('Y-m-d H:i');
        $this->toDT = $to->format('Y-m-d H:i');
    }


    /**
     * @throws \Exception
     */
    private function setDataRangeDefault(): void
    {
        $currentDT = new \DateTimeImmutable('now', new \DateTimeZone($this->timeZone));
        $this->fromDefaultDT = $currentDT->modify('-' . $this->getIntervalDaysDefault() . ' day')->format('Y-m-d 00:00');
        $this->toDefaultDT = $currentDT->modify('-1 day')->format('Y-m-d 23:59');

        $this->dateRange = $this->fromDefaultDT . ' - ' . $this->toDefaultDT;
    }

    /**
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function getEventsForHeatMap(array $params): array
    {
        $query = UserShiftSchedule::find()
            ->innerJoin(Employee::tableName(), 'uss_user_id = employees.id')
            ->andWhere(['<>', 'status', Employee::STATUS_DELETED]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
        }

        $query->select(['uss_start_utc_dt', 'uss_end_utc_dt']);
        $this->queryCondition($query);

        $query->andWhere(['uss_type_id' => ShiftScheduleType::find()->select('sst_id')->andWhere(['sst_subtype_id' => ShiftScheduleType::SUBTYPE_WORK_TIME])])
            ->andWhere(['uss_status_id' => [UserShiftSchedule::STATUS_DONE, UserShiftSchedule::STATUS_APPROVED]]);
        $query->cache($this->cache);

        return array_map(
            function ($item) {
                return [
                    'uss_start_utc_dt' => (new \DateTimeImmutable($item['uss_start_utc_dt']))->setTimezone(new \DateTimeZone($this->timeZone ?: self::DEFAULT_TIMEZONE)),
                    'uss_end_utc_dt' => $item['uss_end_utc_dt'] ? (new \DateTimeImmutable($item['uss_end_utc_dt']))->setTimezone(new \DateTimeZone($this->timeZone ?: self::DEFAULT_TIMEZONE)) : null
                ];
            },
            $query->asArray()->all()
        );
    }

    /**
     * @param ActiveQuery $query
     * @return void
     */
    private function queryCondition(ActiveQuery &$query): void
    {
        $from = QueryHelper::getDateFromUserTZToUtc($this->getFromDT(), $this->timeZone)->format('Y-m-d H:i');
        $to = QueryHelper::getDateFromUserTZToUtc($this->getToDT(), $this->timeZone)->format('Y-m-d H:i');

        $query->andWhere([
            'OR',
            ['between', 'uss_start_utc_dt', $from, $to],
            ['between', 'uss_end_utc_dt', $from, $to],
            [
                'AND',
                ['>=', 'uss_start_utc_dt', $from],
                ['<=', 'uss_end_utc_dt', $to]
            ],
            [
                'AND',
                ['<=', 'uss_start_utc_dt', $from],
                ['>=', 'uss_end_utc_dt', $to]
            ]
        ]);

        if ($this->userGroup) {
            $query->innerJoin([
                'userGroupAssign' => UserGroupAssign::find()
                    ->select(['ugs_user_id'])
                    ->andWhere(['IN', 'ugs_group_id', $this->userGroup])
                    ->groupBy(['ugs_user_id'])
            ], 'userGroupAssign.ugs_user_id = uss_user_id');
        }

        if ($this->shifts) {
            $query->andWhere(['uss_shift_id' => $this->shifts]);
        }

        if ($this->roles) {
            $query->andWhere(['IN', 'uss_user_id', array_keys(Employee::getListByRole($this->roles))]);
        }

        if ($this->department) {
            $subQuery = UserDepartment::find()->usersByDep($this->department);
            $query->andWhere(['IN', 'uss_user_id', $subQuery]);
        }
    }

    /**
     * @return int
     */
    public function getIntervalDaysDefault(): int
    {
        return $this->intervalDaysDefault;
    }

    /**
     * @return string
     */
    public function getToDefaultDT(): string
    {
        return $this->toDefaultDT;
    }

    /**
     * @return string
     */
    public function getFromDefaultDT(): string
    {
        return $this->fromDefaultDT;
    }

    /**
     * @return string
     */
    public function getFromDT(): string
    {
        return $this->fromDT ?: $this->fromDefaultDT;
    }

    /**
     * @return string
     */
    public function getToDT(): string
    {
        return $this->toDT ?: $this->toDefaultDT;
    }
}
